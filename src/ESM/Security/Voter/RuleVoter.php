<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Rule;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class RuleVoter extends Voter
{
    const SHOW = 'RULE_SHOW';
    const EDIT = 'RULE_EDIT';

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
        return in_array($attribute, [self::SHOW], true) || in_array($attribute, [self::EDIT], true) && $subject instanceof Rule;
    }

    protected function voteOnAttribute($attribute, $rule, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::SHOW:
                return $this->canShow();

            case self::EDIT:
                return $this->canEdit($rule) ;
        }

        return false;
    }

    private function canShow(): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_RULE_READ');
    }

    private function canEdit(Rule $rule): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_RULE_WRITE')
			&&  $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $rule->getProject());
    }
}
