<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Center;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CenterVoter extends Voter
{
    const LIST = 'CENTER_LIST';
    const SHOW = 'CENTER_SHOW';
    const EDIT = 'CENTER_EDIT';
    const CREATE = 'CENTER_CREATE';
    const ARCHIVE = 'CENTER_ARCHIVE';
    const RESTORE = 'CENTER_RESTORE';

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
                && $subject instanceof Center);
    }

    protected function voteOnAttribute($attribute, $center, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($center);

            case self::EDIT:
                return $this->canEdit($center);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($center);

            case self::RESTORE:
                return $this->canRestore($center);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_CENTER_READ');
    }

    private function canShow(Center $center): bool
    {
        return $this->security->isGranted('ROLE_CENTER_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_CENTER_WRITE');
    }

    private function canEdit(Center $center): bool
    {
        return $this->security->isGranted('ROLE_CENTER_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $center->getProject())
            && 4 !== $center->getCenterStatus()->getType()
            && null === $center->getDeletedAt();
    }

    private function canArchive(Center $center): bool
    {
        return $this->security->isGranted('ROLE_CENTER_ARCHIVE')
        && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $center->getProject())
        && 1 === $center->getCenterStatus()->getType()
        && null === $center->getDeletedAt();
    }

    private function canRestore(Center $center): bool
    {
        return $this->security->isGranted('ROLE_CENTER_ARCHIVE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $center->getProject())
            && null !== $center->getDeletedAt();
    }
}
