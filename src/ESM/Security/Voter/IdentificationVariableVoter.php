<?php

namespace App\ESM\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class IdentificationVariableVoter extends Voter
{
    const LIST = 'IDENTIFICATIONVARIABLE_LIST';
    const CREATE = 'IDENTIFICATIONVARIABLE_WRITE';

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
        return in_array($attribute, [self::LIST, self::CREATE], true);
    }

    protected function voteOnAttribute($attribute, $visit, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::CREATE:
                return $this->canWrite();
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canWrite(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ') && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');
    }
}
