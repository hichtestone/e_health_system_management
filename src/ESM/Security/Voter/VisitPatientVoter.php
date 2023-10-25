<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\VisitPatient;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class VisitPatientVoter extends Voter
{
    const LIST = 'PATIENTTRACKING_LIST';
    const CREATE = 'PATIENTTRACKING_CREATE';

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
        return in_array($attribute, [self::LIST, self::CREATE], true)
                && $subject instanceof VisitPatient;
    }

    protected function voteOnAttribute($attribute, $visitPatient, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::CREATE:
                return $this->canCreate($visitPatient);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_WRITE')
            || $this->security->isGranted('ROLE_ECRF_READ')
            ;
    }
}
