<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Deviation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DeviationVoter
 * @package App\Security\Voter
 */
class DeviationVoter extends Voter
{
    const CREATE_REVIEW 			 = 'DEVIATION_REVIEW_CREATE';
    const SHOW 						 = 'DEVIATION_SHOW';
    const CREATE_DRAFT 				 = 'DEVIATION_DRAFT_CREATE';
    const CREATE_CORRECTION 		 = 'DEVIATION_CORRECTION_CREATE';
    const EDIT_DRAFT 				 = 'DEVIATION_DRAFT_EDIT';
    const DELETE_DRAFT 				 = 'DEVIATION_DRAFT_DELETE';
    const CREATE_ACTION 			 = 'DEVIATION_ACTION_CREATE_EDIT';
    const DELETE_ACTION 			 = 'DEVIATION_ACTION_DELETE';
    const CLOS 						 = 'DEVIATION_CLOSE';
    const ASSOCIATE_DEVIATION_SAMPLE = 'DEVIATION_ASSOCIATE_DEVIATION_SAMPLE';

    /**
     * @var Security
     */
    private $security;

	/**
	 * DeviationVoter constructor.
	 * @param Security $security
	 */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

	/**
	 * @param string $attribute
	 * @param mixed $subject
	 * @return bool
	 */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::SHOW, self::CREATE_REVIEW, self::CREATE_ACTION, self::CREATE_DRAFT, self::CLOS, self::CREATE_CORRECTION], true)
			|| (in_array($attribute, [self::CREATE_CORRECTION, self::DELETE_ACTION, self::DELETE_DRAFT, self::CREATE_ACTION, self::EDIT_DRAFT, self::ASSOCIATE_DEVIATION_SAMPLE], true)
				&& $subject instanceof Deviation);
    }

	/**
	 * @param string $attribute
	 * @param mixed $deviation
	 * @param TokenInterface $token
	 * @return bool
	 */
    protected function voteOnAttribute($attribute, $deviation, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::SHOW:
                return $this->canShow($deviation);
            case self::CREATE_DRAFT:
                return $this->canCreateDraft();
            case self::CREATE_CORRECTION:
                return $this->canCreateCorrection($deviation);
            case self::CREATE_REVIEW:
                return $this->canCreateReview($deviation);
            case self::CLOS:
                return $this->canClose();
            case self::CREATE_ACTION:
                return $this->canCreateActionDeclaration($deviation);
            case self::DELETE_ACTION:
                return $this->canDeleteAction($deviation);
            case self::EDIT_DRAFT:
                return $this->canEditDraft($deviation);
            case self::DELETE_DRAFT:
                return $this->canDeleteDraft($deviation);
            case self::ASSOCIATE_DEVIATION_SAMPLE:
                return $this->canAssociateDeviationSample($deviation);
        }

        return false;
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canShow(Deviation $deviation): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_READ')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject());
    }

	/**
	 * @return bool
	 */
    private function canCreateDraft(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_WRITE');
    }

	/**
	 * @return bool
	 */
    private function canClose(): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_CLOSE');
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canCreateReview(Deviation $deviation): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_REVIEW')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canCreateActionDeclaration(Deviation $deviation): bool
    {
        return $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && $this->security->isGranted('ROLE_DEVIATION_WRITE')
            && ($deviation->getGrade() === Deviation::GRADE_MAJEUR || $deviation->getGrade() === Deviation::GRADE_CRITIQUE)
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canDeleteAction(Deviation $deviation): bool
    {
        return $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && $this->security->isGranted('ROLE_DEVIATION_ACTION_DELETE')
            && ($deviation->getGrade() === Deviation::GRADE_MAJEUR || $deviation->getGrade() === Deviation::GRADE_CRITIQUE)
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canDeleteDraft(Deviation $deviation): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canEditDraft(Deviation $deviation): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_WRITE')
           && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
    private function canCreateCorrection(Deviation $deviation): bool
    {
        return $this->security->isGranted('ROLE_DEVIATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject())
            && Deviation::STATUS_DONE !== $deviation->getStatus();
    }

	/**
	 * @param Deviation $deviation
	 * @return bool
	 */
	private function canAssociateDeviationSample(Deviation $deviation): bool
	{
		return $this->security->isGranted('ROLE_DEVIATION_ASSOCIATE_SAMPLE')
			&& $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $deviation->getProject());
	}
}
