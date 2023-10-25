<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Publication;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PublicationVoter extends Voter
{
    const LIST = 'PUBLICATION_LIST';
    const SHOW = 'PUBLICATION_SHOW';
    const EDIT = 'PUBLICATION_EDIT';
    const CREATE = 'PUBLICATION_CREATE';
    const ARCHIVE = 'PUBLICATION_ARCHIVE';
    const RESTORE = 'PUBLICATION_RESTORE';

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
                && $subject instanceof Publication);
    }

    protected function voteOnAttribute($attribute, $publication, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($publication);

            case self::EDIT:
                return $this->canEdit($publication);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($publication);

            case self::RESTORE:
                return $this->canRestore($publication);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_READ');
    }

    private function canShow(Publication $publication): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_WRITE');
    }

    private function canEdit(Publication $publication): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $publication->getProject())
			&& null === $publication->getDeletedAt();
    }

    private function canArchive(Publication $publication): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_ARCHIVE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $publication->getProject())
            && null === $publication->getDeletedAt();
    }

    private function canRestore(Publication $publication): bool
    {
        return $this->security->isGranted('ROLE_PUBLICATION_ARCHIVE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $publication->getProject())
            && null !== $publication->getDeletedAt();
    }
}
