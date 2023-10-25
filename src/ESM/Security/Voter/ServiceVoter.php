<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ServiceVoter extends Voter
{
    const EDIT = 'SERVICE_EDIT';
    const ARCHIVE = 'SERVICE_ARCHIVE';
    const RESTORE = 'SERVICE_RESTORE';

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
                && $subject instanceof Service);
    }

    protected function voteOnAttribute($attribute, $institution, TokenInterface $token): bool
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
                return $this->canEdit($institution);

            /*case self::CREATE:
                return $this->canCreate();*/

            case self::ARCHIVE:
                return $this->canArchive($institution);

            case self::RESTORE:
                return $this->canRestore($institution);
        }

        return false;
    }

    private function canEdit(Service $subject): bool
    {
        return $this->security->isGranted('INSTITUTION_EDIT', $subject->getInstitution())
            && null === $subject->getDeletedAt();
    }

    private function canArchive(Service $subject): bool
    {
        // check si présent dans un projet non clos = impossible à archiver
        if ($this->security->isGranted('INSTITUTION_EDIT', $subject->getInstitution())
            && null === $subject->getDeletedAt()) {
            $centerInterlocutors = $subject->getInterlocutorCenters();
            foreach ($centerInterlocutors as $centerInterlocutor) {
                if (null === $centerInterlocutor->getCenter()->getProject()->getClosedAt()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function canRestore(Service $subject): bool
    {
        return $this->security->isGranted('INSTITUTION_EDIT', $subject->getInstitution())
            && null !== $subject->getDeletedAt();
    }
}
