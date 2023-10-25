<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\CourbeSetting;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CourbeSettingVoter extends Voter
{
    const LIST = 'COURBESETTING_LIST';
    const SHOW = 'COURBESETTING_SHOW';
    const EDIT = 'COURBESETTING_EDIT';
    const CREATE = 'COURBESETTING_CREATE';

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
            (in_array($attribute, [self::SHOW, self::EDIT], true)
                && $subject instanceof CourbeSetting);
    }

    protected function voteOnAttribute($attribute, $CourbeSetting, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($CourbeSetting);

            case self::EDIT:
                return $this->canEdit($CourbeSetting);

            case self::CREATE:
                return $this->canCreate();
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('PROJECT_LIST');
    }

    private function canShow(CourbeSetting $CourbeSetting): bool
    {
        return $this->security->isGranted('ROLE_COURBESETTING_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('PROJECT_CREATE');
    }

    private function canEdit(CourbeSetting $CourbeSetting): bool
    {
        return $this->security->isGranted('PROJECT_CREATE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $CourbeSetting->getProject());
    }
}
