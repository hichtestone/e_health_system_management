<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\ProjectDatabaseFreeze;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DatabaseFreezeVoter extends Voter
{
    const EDIT = 'DATABASEFREEZE_EDIT';
    const ARCHIVE = 'DATABASEFREEZE_ARCHIVE';
    const RESTORE = 'DATABASEFREEZE_RESTORE';

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
        return in_array($attribute, [], true) ||
            (in_array($attribute, [self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof ProjectDatabaseFreeze);
    }

    protected function voteOnAttribute($attribute, $databaseFreeze, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            /*case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($institution);*/

            case self::EDIT:
                return $this->canEdit($databaseFreeze);

            /*case self::CREATE:
                return $this->canCreate();*/

            case self::ARCHIVE:
                return $this->canArchive($databaseFreeze);

            case self::RESTORE:
                return $this->canRestore($databaseFreeze);
        }

        return false;
    }

    private function canEdit(ProjectDatabaseFreeze $subject): bool
    {
        return $this->security->isGranted('DATE_EDIT', $subject->getProjectDate())
            && null === $subject->getDeletedAt();
    }

    private function canArchive(ProjectDatabaseFreeze $subject): bool
    {
        return $this->security->isGranted('DATE_EDIT', $subject->getProjectDate())
            && null === $subject->getDeletedAt();
    }

    private function canRestore(ProjectDatabaseFreeze $subject): bool
    {
        return $this->security->isGranted('DATE_EDIT', $subject->getProjectDate())
            && null !== $subject->getDeletedAt();
    }
}
