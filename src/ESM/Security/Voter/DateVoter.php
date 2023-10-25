<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Date;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DateVoter extends Voter
{
    const SHOW = 'DATE_SHOW';
    const EDIT = 'DATE_EDIT';

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
        return in_array($attribute, [self::SHOW], true)
			|| in_array($attribute, [self::EDIT], true) && $subject instanceof Date;
    }

    protected function voteOnAttribute($attribute, $date, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::SHOW:
                return $this->canShow();

            case self::EDIT:
                return $this->canEdit($date);
        }

        return false;
    }

    private function canShow(): bool
    {
        return $this->security->isGranted('ROLE_DATE_READ');
    }

    private function canEdit(Date $date): bool
    {
        return $this->security->isGranted('ROLE_DATE_WRITE')
			&&  $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $date->getProject());
    }
}
