<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\DocumentTracking;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentTrackingVoter extends Voter
{
    const LIST = 'DOCUMENTTRACKING_LIST';
    const SHOW = 'DOCUMENTTRACKING_SHOW';
    const EDIT = 'DOCUMENTTRACKING_EDIT';
    const CREATE = 'DOCUMENTTRACKING_CREATE';
    const ARCHIVE = 'DOCUMENTTRACKING_ARCHIVE';
    const RESTORE = 'DOCUMENTTRACKING_RESTORE';

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
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT], true)
                && $subject instanceof DocumentTracking);
    }

    protected function voteOnAttribute($attribute, $documentTracking, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($documentTracking);

            case self::EDIT:
                return $this->canEdit($documentTracking);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($documentTracking);

            case self::RESTORE:
                return $this->canRestore($documentTracking);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_READ');
    }

    private function canShow(DocumentTracking $documentTracking): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_WRITE');
    }

    private function canEdit(DocumentTracking $documentTracking): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $documentTracking->getProject())
            && null === $documentTracking->getDisabledAt();
    }

    private function canArchive(DocumentTracking $documentTracking): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $documentTracking->getProject())
            && null === $documentTracking->getDisabledAt();
    }

    private function canRestore(DocumentTracking $documentTracking): bool
    {
        return $this->security->isGranted('ROLE_DOCUMENTTRACKING_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $documentTracking->getProject())
            && null !== $documentTracking->getDisabledAt();
    }
}
