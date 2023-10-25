<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Patient;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PatientVoter extends Voter
{
    const LIST = 'PATIENTTRACKING_LIST';
    const SHOW = 'PATIENTTRACKING_SHOW';
    const EDIT = 'PATIENTTRACKING_EDIT';
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
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT], true)
                && $subject instanceof Patient);
    }

    protected function voteOnAttribute($attribute, $patient, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($patient);

            case self::EDIT:
                return $this->canEdit($patient);

            case self::CREATE:
                return $this->canCreate();
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_READ');
    }

    private function canShow(Patient $patient): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_WRITE');
    }

    private function canEdit(Patient $patient): bool
    {
        return $this->security->isGranted('ROLE_PATIENTTRACKING_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $patient->getProject());
    }
}
