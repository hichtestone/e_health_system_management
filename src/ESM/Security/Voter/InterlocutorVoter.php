<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InterlocutorVoter extends Voter
{
    const LIST = 'INTERLOCUTOR_LIST';
    const SHOW = 'INTERLOCUTOR_SHOW';
    const EDIT = 'INTERLOCUTOR_EDIT';
    const CREATE = 'INTERLOCUTOR_CREATE';
    const ARCHIVE = 'INTERLOCUTOR_ARCHIVE';
    const RESTORE = 'INTERLOCUTOR_RESTORE';

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
                && $subject instanceof Interlocutor);
    }

    protected function voteOnAttribute($attribute, $interlocutor, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($interlocutor);

            case self::EDIT:
                return $this->canEdit($interlocutor);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($interlocutor);

            case self::RESTORE:
                return $this->canRestore($interlocutor);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_READ');
    }

    private function canShow(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_WRITE');
    }

    private function canEdit(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_WRITE')
            && null === $interlocutor->getDeletedAt();
    }

    private function canArchive(Interlocutor $interlocutor): bool
    {
        if ($this->security->isGranted('ROLE_INTERLOCUTOR_ARCHIVE')
            && null === $interlocutor->getDeletedAt()) {
            $interlocutorCenters = $interlocutor->getInterlocutorCenters();
            foreach ($interlocutorCenters as $interlocutorCenter) {
                if (null === $interlocutorCenter->getDisabledAt()
                    && 4 !== $interlocutorCenter->getCenter()->getCenterStatus()->getType()
                    && null === $interlocutorCenter->getCenter()->getProject()->getClosedAt()) {
                    return false;
                }
            }
        }

        return true;
    }

    private function canRestore(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_INTERLOCUTOR_ARCHIVE')
            && null !== $interlocutor->getDeletedAt();
    }
}
