<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystemCorrection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviationSystemCorrectionVoter extends Voter
{
	const EDIT_SYSTEM_CORRECTION = 'DEVIATION_SYSTEM_CORRECTION_EDIT';
	const DELETE_SYSTEM_CORRECTION = 'DEVIATION_SYSTEM_CORRECTION_DELETE';

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
		return in_array($attribute, [self::EDIT_SYSTEM_CORRECTION, self::DELETE_SYSTEM_CORRECTION], true) && $subject instanceof DeviationSystemCorrection;
	}

	protected function voteOnAttribute($attribute, $deviationSystemCorrection, TokenInterface $token): bool
	{
		$user = $token->getUser();
		
		if (!$user instanceof UserInterface) {
			return false;
		}

		
		switch ($attribute) {
			case self::EDIT_SYSTEM_CORRECTION:
				return $this->canEditCorrection($deviationSystemCorrection);
			case self::DELETE_SYSTEM_CORRECTION:
				return $this->canDeleteCorrection($deviationSystemCorrection);
		}

		return false;
	}

	/**
	 * @param DeviationSystemCorrection $deviationSystemCorrection
	 * @return bool
	 */
	private function canEditCorrection(DeviationSystemCorrection $deviationSystemCorrection): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSystemCorrection->getDeviationSystem()->getStatus();
	}

	/**
	 * @param DeviationSystemCorrection $deviationSystemCorrection
	 * @return bool
	 */
	private function canDeleteCorrection(DeviationSystemCorrection $deviationSystemCorrection): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_CORRECTION_DELETE')
			&& Deviation::STATUS_DONE !== $deviationSystemCorrection->getDeviationSystem()->getStatus();
	}
}
