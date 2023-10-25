<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\ReportVisit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ReportVisitVoter.
 */
class ReportVisitVoter extends Voter
{
    public const LIST = 'REPORT_VISIT_LIST';
    public const LIST_CENTER = 'REPORT_VISIT_CENTER_LIST';
    public const CREATE = 'REPORT_VISIT_CREATE';
    public const EDIT = 'REPORT_VISIT_EDIT';
    public const REPORT_CREATE = 'REPORT_VISIT_REPORT_CREATE';
    public const REPORT_VALIDATE = 'REPORT_VISIT_REPORT_VALIDATE';
    public const REPORT_DELETE = 'REPORT_VISIT_REPORT_DELETE';
    public const REPORT_DOWNLOAD = 'REPORT_VISIT_REPORT_DOWNLOAD';
    public const REPORT_DOWNLOAD_REPORT = 'REPORT_VISIT_REPORT_DOWNLOAD_REPORT';
    public const REPORT_DOWNLOAD_REPORT_GENERAL = 'REPORT_VISIT_REPORT_DOWNLOAD_REPORT_GENERAL';
    public const NO_VISIT = 'REPORT_VISIT_REPORT_NO_VISIT';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::LIST, self::CREATE, self::LIST_CENTER], true) ||
            (in_array($attribute, [self::EDIT, self::REPORT_VALIDATE, self::REPORT_DELETE, self::REPORT_CREATE, self::EDIT, self::NO_VISIT, self::REPORT_DOWNLOAD, self::REPORT_DOWNLOAD_REPORT, self::REPORT_DOWNLOAD_REPORT_GENERAL], true)
                && $subject instanceof ReportVisit);
    }

    protected function voteOnAttribute($attribute, $reportVisit, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::LIST_CENTER:
                return $this->canCenterList();
            case self::CREATE:
                return $this->canCreate();
            case self::EDIT:
                return $this->canEdit($reportVisit);
            case self::REPORT_CREATE:
                return $this->canReportCreate($reportVisit);
            case self::NO_VISIT:
                return $this->canNoVisit($reportVisit);
            case self::REPORT_DOWNLOAD:
                return $this->canReportDownload($reportVisit);
            case self::REPORT_DELETE:
                return $this->canReportDelete($reportVisit);
            case self::REPORT_VALIDATE:
                return $this->canReportValidate($reportVisit);
            case self::REPORT_DOWNLOAD_REPORT:
                return $this->canReportDownloadReport($reportVisit);
            case self::REPORT_DOWNLOAD_REPORT_GENERAL:
                return $this->canReportDownloadReportGeneral($reportVisit);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_REPORT_READ');
    }

    private function canCenterList(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_REPORT_LIST');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_REPORT_WRITE');
    }

    private function canEdit(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_WRITE')
            && null === $reportVisit->getValidatedAt()
            && null === $reportVisit->getVisitStatus();
    }

    private function canReportCreate(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_WRITE')
            && null === $reportVisit->getValidatedAt()
            && null === $reportVisit->getVisitStatus()
			&& null !== $reportVisit->getExpectedAt();
    }

    private function canNoVisit(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_READ')
            && $this->security->isGranted('ROLE_MONITORING_REPORT_WRITE')
            && null === $reportVisit->getValidatedAt()
            && null === $reportVisit->getVisitStatus();
    }

    private function canReportDownload(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_READ')
            && null != $reportVisit->getReportedAt()
            && ReportVisit::REPORT_STATUS_IN_PROGRESS === $reportVisit->getReportStatus();
    }


    private function canReportDelete(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_WRITE')
            && null != $reportVisit->getReportedAt()
            && ReportVisit::REPORT_STATUS_IN_PROGRESS === $reportVisit->getReportStatus();
    }

    private function canReportValidate(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_UPLOAD')
            && null != $reportVisit->getReportedAt()
            && ReportVisit::REPORT_STATUS_IN_PROGRESS === $reportVisit->getReportStatus();
    }


    private function canReportDownloadReport(ReportVisit $reportVisit): bool
	{
        return $this->security->isGranted('ROLE_MONITORING_REPORT_READ')
            && null != $reportVisit->getReportedAt()
            && ReportVisit::REPORT_STATUS_VALIDATE === $reportVisit->getReportStatus();
    }

	private function canReportDownloadReportGeneral(ReportVisit $reportVisit): bool
	{
		return $this->security->isGranted('ROLE_MONITORING_REPORT_READ')
				&& $this->security->isGranted('ROLE_MONITORING_REPORT_LIST')
			&& null != $reportVisit->getReportedAt()
			&& ReportVisit::REPORT_STATUS_VALIDATE === $reportVisit->getReportStatus();
	}
}
