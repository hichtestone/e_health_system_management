<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportModelVersion;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ReportConfigVersionVoter.
 */
class ReportConfigVersionVoter extends Voter
{
    public const LIST 		= 'REPORT_CONFIG_LIST';
    public const SHOW 		= 'REPORT_CONFIG_SHOW';
    public const ACTIVATE 	= 'REPORT_CONFIG_ACTIVATE';
    public const DEACTIVATE = 'REPORT_CONFIG_DEACTIVATE';
    public const OUTDATED 	= 'REPORT_CONFIG_OUTDATED';
    public const EDIT 		= 'REPORT_CONFIG_EDIT';

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::LIST], true) ||
            (in_array($attribute, [self::SHOW, self::ACTIVATE, self::DEACTIVATE, self::OUTDATED, self::EDIT], true)
                && $subject instanceof ReportConfigVersion);
    }

    protected function voteOnAttribute($attribute, $reportConfigVersion, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::SHOW:
                return $this->canShow();
            case self::ACTIVATE:
                return $this->canActivate($reportConfigVersion);
            case self::DEACTIVATE:
                return $this->canDeactivate($reportConfigVersion);
            case self::OUTDATED:
                return $this->canOutdated($reportConfigVersion);
            case self::EDIT:
                return $this->canEdit($reportConfigVersion);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL');
    }

    private function canShow(): bool
    {
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL');
    }

    private function canActivate(ReportConfigVersion $reportConfigVersion): bool
	{
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL')
            && ReportConfigVersion::STATUS_INACTIVE === $reportConfigVersion->getStatus()
            && ReportModelVersion::STATUS_PUBLISH === $reportConfigVersion->getModelStatus();
    }

    private function canDeactivate(ReportConfigVersion $reportConfigVersion): bool
	{
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL')
            && ReportConfigVersion::STATUS_ACTIVE === $reportConfigVersion->getStatus()
            && ReportModelVersion::STATUS_PUBLISH === $reportConfigVersion->getModelStatus()
            && null == $reportConfigVersion->getModelDeletedAt();
    }

    private function canEdit(ReportConfigVersion $reportConfigVersion): bool
	{
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL')
			// si statut = obsolete impossible de la modifier
			&& ReportModelVersion::STATUS_PUBLISH === $reportConfigVersion->getModelStatus()
			&& null == $reportConfigVersion->getModelDeletedAt();
    }

    private function canOutdated(ReportConfigVersion $reportConfigVersion): bool
	{
        return $this->security->isGranted('ROLE_CONFIGURATION_MONITORING_MODEL')
            && ReportConfigVersion::STATUS_ACTIVE === $reportConfigVersion->getStatus()
            && ReportModelVersion::STATUS_OBSOLETE === $reportConfigVersion->getModelVersion()
            && null === $reportConfigVersion->getModelDeletedAt();
    }
}
