<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DeviationReviewVoter.
 */
class DeviationReviewVoter extends Voter
{
    public const LIST 				= 'DEVIATION_REVIEW_LIST';
    public const SHOW 				= 'DEVIATION_REVIEW_SHOW';
    public const CREX_SHOW 			= 'DEVIATION_REVIEW_CREX_SHOW';
    public const EDIT 				= 'DEVIATION_REVIEW_EDIT';
    public const CREX_EDIT 			= 'DEVIATION_REVIEW_CREX_EDIT';
    public const DELETE 			= 'DEVIATION_REVIEW_DELETE';
    public const CREX_DELETE 		= 'DEVIATION_REVIEW_CREX_DELETE';
    public const CLOSE 				= 'DEVIATION_REVIEW_CLOSE';
    public const CREX_CLOSE 		= 'DEVIATION_REVIEW_CREX_CLOSE';
    public const EDIT_ACTION 		= 'DEVIATION_REVIEW_ACTION_EDIT';
    public const CREX_EDIT_ACTION 	= 'DEVIATION_REVIEW_CREX_ACTION_EDIT';
    public const CREATE 			= 'DEVIATION_REVIEW_CREATE_BTN';
    public const CREATE_ACTION 		= 'DEVIATION_REVIEW_ACTION_CREATE';
    public const CREX_CREATE_ACTION = 'DEVIATION_REVIEW_CREX_ACTION_CREATE';
    public const DELETE_ACTION 		= 'DEVIATION_REVIEW_ACTION_DELETE';
    public const CREX_DELETE_ACTION = 'DEVIATION_REVIEW_CREX_ACTION_DELETE';

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
        return in_array($attribute, [self::LIST, self::CREATE, self::CREX_CREATE_ACTION], true) ||
            (in_array($attribute, [self::SHOW, self::CREX_SHOW, self::EDIT, self::CREX_EDIT, self::DELETE, self::CREX_DELETE, self::CLOSE, self::CREX_CLOSE, self::CREATE_ACTION, self::EDIT_ACTION, self::CREX_EDIT_ACTION, self::DELETE_ACTION, self::CREX_DELETE_ACTION], true)
                && $subject instanceof DeviationReview);
    }

    protected function voteOnAttribute($attribute, $deviationReview, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($deviationReview);

            case self::CREX_SHOW:
                return $this->canCrexShow($deviationReview);

            case self::EDIT:
                return $this->canEdit($deviationReview);

            case self::CREX_EDIT:
                return $this->canCrexEdit($deviationReview);

            case self::DELETE:
                return $this->canDelete($deviationReview);

            case self::CREX_DELETE:
                return $this->canCrexDelete($deviationReview);

            case self::CLOSE:
                return $this->canClose($deviationReview);

            case self::CREX_CLOSE:
                return $this->canCrexClose($deviationReview);

            case self::EDIT_ACTION:
                return $this->canEditionAction($deviationReview);

            case self::CREX_EDIT_ACTION:
                return $this->canCrexEditionAction($deviationReview);

            case self::CREATE:
                return $this->canCreate();

            case self::CREATE_ACTION:
                return $this->canCreateAction($deviationReview);

            case self::CREX_CREATE_ACTION:
                return $this->canCrexCreateAction($deviationReview);

            case self::DELETE_ACTION:
                return $this->canDeleteAction($deviationReview);

            case self::CREX_DELETE_ACTION:
                return $this->canCrexDeleteAction($deviationReview);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_READ');
    }

    private function canShow(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_READ')
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }

    private function canCrexShow(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_READ')
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW');
    }

    private function canCreateAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt();
    }

    private function canCrexCreateAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX_READ')
            && $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt();
    }

    private function canEdit(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }

    private function canCrexEdit(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

    private function canEditionAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }

    private function canCrexEditionAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

    private function canDelete(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }

    private function canCrexDelete(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

    private function canClose(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }

    private function canCrexClose(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

    private function canDeleteAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviationReview->getDeviation()->getProject())
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && false === $deviationReview->getIsCrex();
    }
    private function canCrexDeleteAction(DeviationReview $deviationReview): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW_CREX')
            && Deviation::STATUS_DONE !== $deviationReview->getDeviation()->getStatus()
            && DeviationReview::STATUS_FINISH !== $deviationReview->getStatus()
            && null === $deviationReview->getDeletedAt()
            && true === $deviationReview->getIsCrex();
    }

}
