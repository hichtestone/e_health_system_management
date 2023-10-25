<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSample;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviationSampleVoter extends Voter
{
    const LIST = 'DEVIATION_SAMPLE_LIST';
    const CREATE = 'DEVIATION_SAMPLE_CREATE';
	const CREATE_CORRECTION = 'DEVIATION_SAMPLE_CORRECTION_CREATE';
	const CREATE_DRAFT = 'DEVIATION_SAMPLE_DRAFT_CREATE';
	const EDIT_DRAFT = 'DEVIATION_SAMPLE_DRAFT_EDIT';
	const DELETE_DRAFT = 'DEVIATION_SAMPLE_DRAFT_DELETE';
	const CREATE_ACTION = 'DEVIATION_SAMPLE_ACTION_CREATE_EDIT';
	const DELETE_ACTION = 'DEVIATION_SAMPLE_ACTION_DELETE';
	const ASSOCIATE_DEVIATION = 'DEVIATION_SAMPLE_ASSOCIATE_DEVIATION';
	const CLOSE = 'DEVIATION_SAMPLE_CLOSE';
	const CLOSE_MULTI = 'DEVIATION_SAMPLE_CLOSE_MULTI';

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
		return in_array($attribute, [self::LIST, self::CREATE, self::CREATE_DRAFT, self::CLOSE_MULTI], true)
			|| (in_array($attribute, [self::CREATE_CORRECTION, self::EDIT_DRAFT, self::DELETE_DRAFT, self::CREATE_ACTION, self::DELETE_ACTION, self::ASSOCIATE_DEVIATION, self::CLOSE], true)
				&& $subject instanceof DeviationSample);
	}

    protected function voteOnAttribute($attribute, $deviationSample, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::CREATE:
                return $this->canCreate();
			case self::CREATE_CORRECTION:
				return $this->canCreateCorrection($deviationSample);
			case self::CREATE_DRAFT:
				return $this->canCreateDraft();
			case self::EDIT_DRAFT:
				return $this->canEditDraft($deviationSample);
			case self::DELETE_DRAFT:
				return $this->canDeleteDraft($deviationSample);
			case self::DELETE_ACTION:
				return $this->canDeleteAction($deviationSample);
			case self::CREATE_ACTION:
				return $this->canCreateActionDeclaration($deviationSample);
			case self::ASSOCIATE_DEVIATION:
				return $this->canAssociateDeviation($deviationSample);
			case self::CLOSE:
				return $this->canClose($deviationSample);
			case self::CLOSE_MULTI:
				return $this->canCloseMulti();
        }

        return false;
    }

	/**
	 * @return bool
	 */
    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_READ');
    }

	/**
	 * @return bool
	 */
    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE');
    }

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canCreateCorrection(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @return bool
	 */
	private function canCreateDraft(): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE');
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canEditDraft(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canDeleteDraft(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canCreateActionDeclaration(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_WRITE')
			&& ($deviationSample->getGrade() === Deviation::GRADE_MAJEUR || $deviationSample->getGrade() === Deviation::GRADE_CRITIQUE)
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canDeleteAction(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_ACTION_DELETE')
			&& ($deviationSample->getGrade() === Deviation::GRADE_MAJEUR || $deviationSample->getGrade() === Deviation::GRADE_CRITIQUE)
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canAssociateDeviation(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_ASSOCIATE_DEVIATION')
			&& Deviation::STATUS_IN_PROGRESS === $deviationSample->getStatus();
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return bool
	 */
	private function canClose(DeviationSample $deviationSample): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_CLOSE')
			&& Deviation::STATUS_DONE !== $deviationSample->getStatus();
	}

	/**
	 * @return bool
	 */
	private function canCloseMulti(): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_SAMPLE_CLOSE');
	}

}
