<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Meeting;
use App\ESM\Entity\Training;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TrainingVoter extends Voter
{
    const LIST = 'TRAINING_LIST';
    const SHOW = 'TRAINING_SHOW';
    const EDIT = 'TRAINING_EDIT';
    const CREATE = 'TRAINING_CREATE';
    const DELETE = 'TRAINING_DELETE';
    //const ARCHIVE = 'MEETING_ARCHIVE'; mp: pas besoin a priori

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
            (in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true)
                && $subject instanceof Training);
    }

    protected function voteOnAttribute($attribute, $training, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($training);

            case self::EDIT:
                return $this->canEdit($training);

            case self::CREATE:
                return $this->canCreate();

            case self::DELETE:
                return $this->canDelete($training);
        }

        return false;
    }

	/**
	 * @return bool
	 */
    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_READ');
    }

	/**
	 * @param Training $training
	 * @return bool
	 */
    private function canShow(Training $training): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_READ');
    }

	/**
	 * @return bool
	 */
    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_WRITE');
    }

	/**
	 * @param Training $training
	 * @return bool
	 */
    private function canEdit(Training $training): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $training->getProject());
    }

	/**
	 * @param Training $training
	 * @return bool
	 */
    private function canDelete(Training $training): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_DELETE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $training->getProject());
    }
}
