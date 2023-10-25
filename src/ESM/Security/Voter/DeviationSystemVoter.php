<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DeviationSystemVoter
 * @package App\Security\Voter
 */
class DeviationSystemVoter extends Voter
{
	public const LIST        		= 'DEVIATION_SYSTEM_LIST';
	public const LIST_REVIEW 		= 'DEVIATION_SYSTEM_REVIEW_LIST';
	public const SHOW        		= 'DEVIATION_SYSTEM_SHOW';
	public const CLOSE 				= 'DEVIATION_SYSTEM_CLOSE';
	public const CREATE_REVIEW 		= 'DEVIATION_SYSTEM_REVIEW_CREATE';
	public const CREATE_DRAFT  		= 'DEVIATION_SYSTEM_DRAFT_CREATE';
	public const EDIT_DRAFT   		= 'DEVIATION_SYSTEM_DRAFT_EDIT';
	public const DELETE_DRAFT 		= 'DEVIATION_SYSTEM_DRAFT_DELETE';
	public const CREATE_CORRECTION 	= 'DEVIATION_SYSTEM_CORRECTION_CREATE';
	public const CREATE_ACTION     	= 'DEVIATION_SYSTEM_ACTION_CREATE';
	public const EDIT_ACTION   		= 'DEVIATION_SYSTEM_ACTION_EDIT';
	public const DELETE_ACTION 		= 'DEVIATION_SYSTEM_ACTION_DELETE';
	public const CLOSE_MULTI   		= 'DEVIATION_SYSTEM_CLOSE_MULTI';
	public const QA_WRITE   		= 'DEVIATION_SYSTEM_QA_WRITE';

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
		return in_array($attribute, [self::LIST, self::LIST_REVIEW, self::SHOW, self::CREATE_REVIEW, self::CREATE_DRAFT, self::CREATE_ACTION, self::CLOSE_MULTI, self::EDIT_ACTION, self::DELETE_ACTION, self::QA_WRITE], true) ||
			(in_array($attribute, [self::CLOSE, self::CREATE_CORRECTION, self::CREATE_ACTION, self::EDIT_ACTION, self::DELETE_ACTION, self::EDIT_DRAFT, self::DELETE_DRAFT, self::QA_WRITE], true)
				&& $subject instanceof DeviationSystem);
	}

	protected function voteOnAttribute($attribute, $deviationSystem, TokenInterface $token): bool
	{
		$user = $token->getUser();

		if (!$user instanceof UserInterface) {
			return false;
		}

		switch ($attribute) {
			case self::LIST:
				return $this->canList();
			case self::LIST_REVIEW:
				return $this->canListReview();
			case self::SHOW:
				return $this->canShow($deviationSystem);
			case self::CREATE_DRAFT:
				return $this->canCreateDraft($deviationSystem);
			case self::CREATE_CORRECTION:
				return $this->canCreateCorrection($deviationSystem);
			case self::CREATE_REVIEW:
				return $this->canCreateReview($deviationSystem);
			case self::CLOSE:
				return $this->canClose($deviationSystem);
			case self::CREATE_ACTION:
				return $this->canCreateAction($deviationSystem);
			case self::EDIT_ACTION:
				return $this->canEditAction($deviationSystem);
			case self::DELETE_ACTION:
				return $this->canDeleteAction($deviationSystem);
			case self::EDIT_DRAFT:
				return $this->canEditDraft($deviationSystem);
			case self::DELETE_DRAFT:
				return $this->canDeleteDraft($deviationSystem);
			case self::CLOSE_MULTI:
				return $this->canCloseMulti();
			case self::QA_WRITE:
				return $this->canEditQA();
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function canList(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_READ');
	}

	/**
	 * @return bool
	 */
	private function canListReview(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_CREX_READ');
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canShow(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_READ');
	}

	/**
	 * @return bool
	 */
	private function canCreateDraft(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE');
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canClose(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_CLOSE')
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canCreateReview(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canCreateAction(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& ($deviationSystem->getGrade() === Deviation::GRADE_MAJEUR || $deviationSystem->getGrade() === Deviation::GRADE_CRITIQUE)
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canEditAction(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& ($deviationSystem->getGrade() === Deviation::GRADE_MAJEUR || $deviationSystem->getGrade() === Deviation::GRADE_CRITIQUE)
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canDeleteAction(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_ACTION_DELETE')
			&& ($deviationSystem->getGrade() === Deviation::GRADE_MAJEUR || $deviationSystem->getGrade() === Deviation::GRADE_CRITIQUE)
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canDeleteDraft(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& Deviation::STATUS_DRAFT === $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canEditDraft(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return bool
	 */
	private function canCreateCorrection(DeviationSystem $deviationSystem): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_WRITE')
			&& Deviation::STATUS_DONE !== $deviationSystem->getStatus();
	}

	/**
	 * @return bool
	 */
	private function canCloseMulti(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_CLOSE');
	}

	/**
	 * @return bool
	 */
	private function canEditQA(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE');
	}
}
