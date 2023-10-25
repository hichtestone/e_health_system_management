<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Meeting;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MeetingVoter extends Voter
{
    const LIST = 'MEETING_LIST';
    const SHOW = 'MEETING_SHOW';
    const EDIT = 'MEETING_EDIT';
    const CREATE = 'MEETING_CREATE';
    const DELETE = 'MEETING_DELETE';
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
                && $subject instanceof Meeting);
    }

    protected function voteOnAttribute($attribute, $meeting, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($meeting);

            case self::EDIT:
                return $this->canEdit($meeting);

            case self::CREATE:
                return $this->canCreate();

            case self::DELETE:
                return $this->canDelete($meeting);
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
	 * @param Meeting $meeting
	 * @return bool
	 */
    private function canShow(Meeting $meeting): bool
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
	 * @param Meeting $meeting
	 * @return bool
	 */
    private function canEdit(Meeting $meeting): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $meeting->getProject());
    }

	/**
	 * @param Meeting $meeting
	 * @return bool
	 */
    private function canDelete(Meeting $meeting): bool
    {
        return $this->security->isGranted('ROLE_COMMUNICATION_DELETE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $meeting->getProject());
    }
}
