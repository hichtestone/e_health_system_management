<?php

namespace App\ESM\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class EcrfSettingVoter extends Voter
{
    const LIST = 'ECRFSETTING_LIST';
    const CREATE = 'DIAGRAM_VISIT_AND_IDENTIFICATION_ECRF';

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

    protected function voteOnAttribute($attribute, $ecrf, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate();
            case self::LIST:
                return $this->canList();
        }

        return false;
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ') && $this->security->isGranted('ROLE_ECRF_READ');
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }
}
