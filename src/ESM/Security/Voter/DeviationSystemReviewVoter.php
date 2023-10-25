<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSystemReview;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DeviationSystemReviewVoter
 * @package App\Security\Voter
 */
class DeviationSystemReviewVoter extends Voter
{
	public const LIST          = 'DEVIATION_SYSTEM_REVIEW_List';
	public const SHOW          = 'DEVIATION_SYSTEM_REVIEW_SHOW';
	public const EDIT          = 'DEVIATION_SYSTEM_REVIEW_EDIT';
	public const DELETE        = 'DEVIATION_SYSTEM_REVIEW_DELETE';
	public const CLOSE         = 'DEVIATION_SYSTEM_REVIEW_CLOSE';
	public const ACTION_CREATE = 'DEVIATION_SYSTEM_REVIEW_ACTION_CREATE';
	public const ACTION_EDIT   = 'DEVIATION_SYSTEM_REVIEW_ACTION_EDIT';
	public const ACTION_DELETE  = 'DEVIATION_SYSTEM_REVIEW_ACTION_DELETE';

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
		return in_array($attribute, [self::LIST], true) ||
			(in_array($attribute, [self::SHOW, self::EDIT, self::DELETE, self::CLOSE, self::ACTION_CREATE, self::ACTION_EDIT, self::ACTION_DELETE], true) && $subject instanceof DeviationSystemReview);
	}

	/**
	 * @param string $attribute
	 * @param mixed $deviationSystemReview
	 * @param TokenInterface $token
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $deviationSystemReview, TokenInterface $token): bool
	{
		$user = $token->getUser();
		
		if (!$user instanceof UserInterface) {
			return false;
		}

		switch ($attribute) {
			case self::LIST:
				return $this->canList();
			case self::SHOW:
				return $this->canShow($deviationSystemReview);
			case self::EDIT:
				return $this->canEdit($deviationSystemReview);
			case self::DELETE:
				return $this->canDelete($deviationSystemReview);
			case self::CLOSE:
				return $this->canClose($deviationSystemReview);
			case self::ACTION_CREATE:
				return $this->canCreateAction($deviationSystemReview);
			case self::ACTION_EDIT:
				return $this->canEditAction($deviationSystemReview);
			case self::ACTION_DELETE:
				return $this->canDeleteAction($deviationSystemReview);
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function canList(): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_CREX_READ');
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canShow(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& null === $deviationSystemReview->getDeletedAt();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canEdit(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canDelete(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canClose(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canCreateAction(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canEditAction(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}

	/**
	 * @param DeviationSystemReview $deviationSystemReview
	 * @return bool
	 */
	private function canDeleteAction(DeviationSystemReview $deviationSystemReview): bool
	{
		return $this->security->isGranted('ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX')
			&& Deviation::STATUS_DONE !== $deviationSystemReview->getDeviationSystem()->getStatus()
			&& DeviationReview::STATUS_FINISH !== $deviationSystemReview->getStatus()
			&& null === $deviationSystemReview->getDeletedAt()
			&& true === $deviationSystemReview->getIsCrex();
	}
}
