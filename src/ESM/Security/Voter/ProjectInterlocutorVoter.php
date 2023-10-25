<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectInterlocutorVoter extends Voter
{
    const LIST = 'PROJECTINTERLOCUTOR_LIST';
    const SHOW = 'PROJECTINTERLOCUTOR_SHOW';
    const EDIT = 'PROJECTINTERLOCUTOR_EDIT';

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
        return in_array($attribute, [self::LIST], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT], true)
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
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_READ');
    }

    private function canShow(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_READ');
    }

    private function canEdit(Interlocutor $interlocutor): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_WRITE')
            && null === $interlocutor->getDeletedAt();
    }
}
