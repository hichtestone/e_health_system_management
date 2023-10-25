<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\ReportBlock;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ReportBlockVoter.
 */
class ReportBlockVoter extends Voter
{
    public const EDIT = 'REPORT_BLOCK_EDIT';
    public const CREATE = 'REPORT_BLOCK_CREATE';
    public const ORDER = 'REPORT_BLOCK_ORDER';
    public const RENAME = 'REPORT_BLOCK_RENAME';
    public const DELETE = 'REPORT_BLOCK_DELETE';

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
        return in_array($attribute, [self::CREATE, self::ORDER], true) ||
            (in_array($attribute, [self::EDIT, self::RENAME, self::DELETE], true)
                && $subject instanceof ReportBlock);
    }

    protected function voteOnAttribute($attribute, $reportBlock, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($reportBlock);
            case self::RENAME:
                return $this->canRename($reportBlock);
            case self::DELETE:
                return $this->canDelete($reportBlock);
            case self::CREATE:
                return $this->canCreate();
            case self::ORDER:
                return $this->canOrder();
        }

        return false;
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }

    private function canOrder(): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }

    private function canEdit(ReportBlock $reportBlock): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }

    private function canRename(ReportBlock $reportBlock): bool
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }

    private function canDelete(ReportBlock $reportBlock)
    {
        return $this->security->isGranted('ROLE_MONITORING_MODEL_WRITE');
    }
}
