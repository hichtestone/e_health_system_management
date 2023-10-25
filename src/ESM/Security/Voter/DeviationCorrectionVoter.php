<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationCorrection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviationCorrectionVoter extends Voter
{
    const EDIT_CORRECTION = 'DEVIATION_CORRECTION_EDIT';
    const DELETE_CORRECTION = 'DEVIATION_CORRECTION_DELETE';

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
        return in_array($attribute, [self::EDIT_CORRECTION, self::DELETE_CORRECTION], true) && $subject instanceof DeviationCorrection;
    }

    protected function voteOnAttribute($attribute, $deviationCorrection, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::EDIT_CORRECTION:
                return $this->canEditCorrection($deviationCorrection);
            case self::DELETE_CORRECTION:
                return $this->canDeleteCorrection($deviationCorrection);
        }

        return false;
    }

    private function canEditCorrection(DeviationCorrection $deviationCorrection)
    {
        return $this->security->isGranted('ROLE_DEVIATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationCorrection->getProject())
            && Deviation::STATUS_DONE != $deviationCorrection->getDeviation()->getStatus();
    }

    private function canDeleteCorrection(DeviationCorrection $deviationCorrection)
    {
        return $this->security->isGranted('ROLE_DEVIATION_CORRECTION_DELETE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationCorrection->getProject())
            && Deviation::STATUS_DONE != $deviationCorrection->getDeviation()->getStatus();
    }
}
