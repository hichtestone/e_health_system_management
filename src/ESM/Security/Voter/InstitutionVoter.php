<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InstitutionVoter extends Voter
{
    const LIST = 'INSTITUTION_LIST';
    const SHOW = 'INSTITUTION_SHOW';
    const EDIT = 'INSTITUTION_EDIT';
    const CREATE = 'INSTITUTION_CREATE';
    const ARCHIVE = 'INSTITUTION_ARCHIVE';
    const RESTORE = 'INSTITUTION_RESTORE';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof Institution);
    }

    protected function voteOnAttribute($attribute, $institution, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($institution);

            case self::EDIT:
                return $this->canEdit($institution);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($institution);

            case self::RESTORE:
                return $this->canRestore($institution);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_READ');
    }

    private function canShow(Institution $subject): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_WRITE');
    }

    private function canEdit(Institution $subject): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_WRITE')
            && !(null !== $subject->getDeletedAt());
    }

    private function canArchive(Institution $subject): bool
    {
        // check si présent dans un projet non clos = impossible à archiver
        if ($this->security->isGranted('ROLE_INSTITUTION_ARCHIVE')
            && null === $subject->getDeletedAt()) {
            $centers = $subject->getCenters();
            foreach ($centers as $center) {
                if (null === $center->getDeletedAt()) {
                    if (null === $center->getProject()->getClosedAt()) {
                        return false;
                    }
                }
            }
            $interlocutors = $subject->getInterlocutors();
            foreach ($interlocutors as $interlocutor) {
                if (null === $interlocutor->getDeletedAt()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function canRestore(Institution $subject): bool
    {
        return $this->security->isGranted('ROLE_INSTITUTION_ARCHIVE')
            && null !== $subject->getDeletedAt();
    }
}
