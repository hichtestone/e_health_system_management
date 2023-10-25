<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\DeviationSampleCorrection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviationSampleCorrectionVoter extends Voter
{
    const EDIT_SAMPLE_CORRECTION = 'DEVIATION_SAMPLE_CORRECTION_EDIT';
    const DELETE_SAMPLE_CORRECTION = 'DEVIATION_SAMPLE_CORRECTION_DELETE';

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
        return in_array($attribute, [self::EDIT_SAMPLE_CORRECTION, self::DELETE_SAMPLE_CORRECTION], true) && $subject instanceof DeviationSampleCorrection;
    }

    protected function voteOnAttribute($attribute, $deviationSampleCorrection, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::EDIT_SAMPLE_CORRECTION:
                return $this->canEditCorrection($deviationSampleCorrection);
            case self::DELETE_SAMPLE_CORRECTION:
                return $this->canDeleteCorrection($deviationSampleCorrection);
        }

        return false;
    }

	/**
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @return bool
	 */
    private function canEditCorrection(DeviationSampleCorrection $deviationSampleCorrection): bool
	{
        return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE')
            && Deviation::STATUS_DONE !== $deviationSampleCorrection->getDeviationSample()->getStatus();
    }

	/**
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @return bool
	 */
    private function canDeleteCorrection(DeviationSampleCorrection $deviationSampleCorrection): bool
	{
        return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_CORRECTION_DELETE')
            && Deviation::STATUS_DONE !== $deviationSampleCorrection->getDeviationSample()->getStatus();
    }
}
