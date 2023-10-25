<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Profile;
use App\ESM\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    const LIST = 'PROFILE_LIST';
    const SHOW = 'PROFILE_SHOW';
    const EDIT = 'PROFILE_EDIT';
    const CREATE = 'PROFILE_CREATE';
    const ARCHIVE = 'PROFILE_ARCHIVE';
    const RESTORE = 'PROFILE_RESTORE';

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
            (in_array($attribute, [self::SHOW, self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof Profile);
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
                return $this->canList($user);

            case self::SHOW:
                // logic to determine if the user can SHOW
                return $this->canShow($subject, $user);

            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($subject, $user);

            case self::CREATE:
                // logic to determine if the user can CREATE
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($subject, $user);

            case self::RESTORE:
                return $this->canRestore($subject, $user);
        }

        return false;
    }

    private function canShow(Profile $profile, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_READ') || $user->getIsSuperAdmin();
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_WRITE');
    }

    private function canEdit(Profile $profile, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_WRITE') || $user->getIsSuperAdmin();
    }

    private function canList(UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_READ') || $user->getIsSuperAdmin();
    }

    private function canArchive(Profile $profile, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_WRITE')
            && null === $profile->getDeletedAt()
            && 0 === count($profile->getUsers())
            ;
    }

    private function canRestore(Profile $profile, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROFILE_WRITE')
            && null !== $profile->getDeletedAt()
            ;
    }
}
