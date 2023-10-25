<?php

namespace App\ESM\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class NavigationVoter extends Voter
{
    const CENTER = 'MENU_CENTER';
    const ADMIN = 'MENU_ADMIN';

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
        return in_array($attribute, [self::ADMIN, self::CENTER], true);
    }

    protected function voteOnAttribute($attribute, $meeting, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::ADMIN:
                return $this->canAdmin();

            case self::CENTER:
                return $this->canCenter();
        }

        return false;
    }

    public function canAdmin(): bool
    {
        return $this->security->isGranted('PROJECT_LIST')
            || $this->security->isGranted('USER_LIST')
            || $this->security->isGranted('INSTITUTION_LIST')
            || $this->security->isGranted('PROFILE_LIST')
            || $this->security->isGranted('INTERLOCUTOR_LIST')
            || $this->security->isGranted('ROLE_SHOW_AUDIT_TRAIL')
            || $this->security->isGranted('ROLE_DRUG_READ')
            ;
    }

    private function canCenter(): bool
    {
        return $this->security->isGranted('ROLE_CENTER_READ')
            || $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_READ')
            || $this->security->isGranted('ROLE_DOCUMENTTRACKING_READ')
            || $this->security->isGranted('ROLE_PATIENTTRACKING_READ')
            ;
    }
}
