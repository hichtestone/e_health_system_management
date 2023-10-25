<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Submission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SubmissionVoter extends Voter
{
    const LIST = 'SUBMISSION_LIST';
    const SHOW = 'SUBMISSION_SHOW';
    const EDIT = 'SUBMISSION_EDIT';
    const CREATE = 'SUBMISSION_CREATE';
    const ARCHIVE = 'SUBMISSION_ARCHIVE';
    const RESTORE = 'SUBMISSION_RESTORE';

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
            (in_array($attribute, [self::SHOW, self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof Submission);
    }

    protected function voteOnAttribute($attribute, $submission, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($submission);

            case self::EDIT:
                return $this->canEdit($submission);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($submission);

            case self::RESTORE:
                return $this->canRestore($submission);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_READ');
    }

    private function canShow(Submission $submission): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_WRITE');
    }

    private function canEdit(Submission $submission): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $submission->getProject())
            && null === $submission->getDeletedAt();
    }

    private function canArchive(Submission $submission): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_ARCHIVE')
            && null === $submission->getDeletedAt();
    }

    private function canRestore(Submission $submission): bool
    {
        return $this->security->isGranted('ROLE_SUBMISSION_ARCHIVE')
            && null !== $submission->getDeletedAt();
    }
}
