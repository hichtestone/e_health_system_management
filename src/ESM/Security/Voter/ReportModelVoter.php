<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ReportModelVoter.
 */
class ReportModelVoter extends Voter
{
    public const LIST = 'REPORT_MODEL_LIST';
    public const SHOW = 'REPORT_MODEL_SHOW';
    public const SHOW_VERSION = 'REPORT_MODEL_VERSION_SHOW';
    public const EDIT = 'REPORT_MODEL_EDIT';
    public const CREATE = 'REPORT_MODEL_CREATE';
    public const DELETE = 'REPORT_MODEL_DELETE';
    public const CREATE_VERSION = 'REPORT_MODEL_CREATE_VERSION';

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
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT, self::DELETE, self::CREATE_VERSION], true)
                && $subject instanceof ReportModel) ||
            (in_array($attribute, [self::SHOW_VERSION], true)
                && $subject instanceof ReportModelVersion);
    }

    protected function voteOnAttribute($attribute, $entity, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::SHOW:
                return $this->canShow($entity);
            case self::SHOW_VERSION:
                return $this->canShowVersion($entity);
            case self::EDIT:
                return $this->canEdit($entity);
            case self::DELETE:
                return $this->canDelete($entity);
            case self::CREATE_VERSION:
                return $this->canCreateVersion($entity);
            case self::CREATE:
                return $this->canCreate();
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_READ');
    }

    private function canShow(ReportModel $reportModel): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_READ');
    }

    private function canShowVersion(ReportModelVersion $reportModelVersion): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }

    private function canEdit(ReportModel $reportModel): bool
    {
    	return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE')
            && null === $reportModel->getDeletedAt();
    }

    private function canDelete(ReportModel $reportModel)
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE')
            && null === $reportModel->getDeletedAt();
    }

    private function canCreateVersion(ReportModel $reportModel)
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE')
            && null === $reportModel->getDeletedAt();
    }
}
