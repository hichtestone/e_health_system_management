<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const LIST = 'USER_LIST';
    const SHOW = 'USER_SHOW';
    const EDIT = 'USER_EDIT';
    const CREATE = 'USER_CREATE';
    const ACCESS = 'USER_ACCESS';
    const ARCHIVE = 'USER_ARCHIVE';
    const RESTORE = 'USER_RESTORE';

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
        (in_array($attribute, [self::SHOW, self::EDIT, self::ACCESS, self::ARCHIVE, self::RESTORE], true)
            && $subject instanceof User);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                // logic to determine if the user can LIST
                // return true or false
                return $this->canList();
            case self::SHOW:
                // logic to determine if the user can SHOW
                // return true or false
                return $this->canShow($subject, $user);

            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($subject);

            case self::CREATE:
                // logic to determine if the user can CREATE
                // return true or false
                return $this->canCreate();

            case self::ACCESS:
                return $this->canAccess($subject, $user);

            case self::ARCHIVE:
                return $this->canArchive($subject, $user);

            case self::RESTORE:
                return $this->canRestore($subject, $user);
        }

        return false;
    }

    // si current user ou droit show
    private function canAccess(User $subject, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_USER_ACCESS')
            && $subject->getId() !== $user->getId()
            && null === $subject->getDeletedAt();
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_USER_READ');
    }

    // si current user ou droit show
    private function canShow(User $subject, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_USER_READ');
    }

    // droit edit
    private function canEdit(User $subject): bool
    {
        return $this->security->isGranted('ROLE_USER_WRITE')
            && !(null !== $subject->getDeletedAt());
    }

    // droit create
    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_USER_WRITE');
    }

    // droit create
    private function canArchive(User $subject, UserInterface $user): bool
    {
        if ($subject->getIsSuperAdmin()) {
            return false;
        }

        return $this->security->isGranted('ROLE_USER_ARCHIVE')
            && $user->getId() !== $subject->getId()
            && null === $subject->getDeletedAt();
    }

    private function canRestore(User $subject): bool
    {
        if ($subject->getIsSuperAdmin()) {
            return false;
        }
        
        return $this->security->isGranted('ROLE_USER_ARCHIVE')
            && null !== $subject->getDeletedAt();
    }
}
