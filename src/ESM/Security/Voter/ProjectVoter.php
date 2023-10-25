<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    public const LIST 			 = 'PROJECT_LIST';
    public const SHOW 			 = 'PROJECT_SHOW';
    public const EDIT   		 = 'PROJECT_EDIT';
    public const CREATE 		 = 'PROJECT_CREATE';
    public const CLOSE 			 = 'PROJECT_CLOSE';
    public const OPEN         	 = 'PROJECT_OPEN';
    public const CLOSE_DEMAND 	 = 'PROJECT_CLOSE_DEMAND';
    public const ACCESS   		 = 'PROJECT_ACCESS';
    public const WRITE           = 'PROJECT_WRITE';
    public const ACCESS_AND_OPEN = 'PROJECT_ACCESS_AND_OPEN';

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
            (in_array($attribute, [self::SHOW, self::EDIT, self::CLOSE, self::OPEN, self::CLOSE_DEMAND, self::ACCESS, self::WRITE, self::ACCESS_AND_OPEN], true)
                && $subject instanceof Project);
    }

    protected function voteOnAttribute($attribute, $project, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {

            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($project, $user);

            case self::EDIT:
                return $this->canEdit($project, $user);

            case self::CREATE:
                return $this->canCreate();

            case self::CLOSE_DEMAND:
                return $this->canCloseDemand($project, $user);

            case self::CLOSE:
                return $this->canClose($project, $user);

            case self::OPEN:
                return $this->canOpen($project, $user);

            case self::ACCESS:
                return $this->canAccess($project, $user);

			case self::WRITE:
				return $this->canWrite($project, $user);

            case self::ACCESS_AND_OPEN:
                return $this->canAccessAndOpen($project, $user);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_READ');
    }

    private function canShow(Project $project, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_READ') &&
            (null === $project->getClosedAt() || $this->security->isGranted('ROLE_PROJECT_READ_CLOSED'));
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_WRITE');
    }

    private function canEdit(Project $project, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_WRITE') && null === $project->getClosedAt();
    }

    private function canCloseDemand(Project $project, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_CLOSE_DEMAND') && null === $project->getCloseDemandedAt();
    }

    private function canClose(Project $project, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_CLOSE') && null === $project->getClosedAt() && null !== $project->getCloseDemandedAt();
    }

    private function canOpen(Project $project, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PROJECT_CLOSE') && null !== $project->getClosedAt();
    }

    /**
     * check si on peut entrer dans un projet en lecture.
     */
    private function canAccess(Project $project, UserInterface $user): bool
    {
        return $project->hasUser($user) &&
            (null === $project->getClosedAt() || $this->security->isGranted('ROLE_PROJECT_READ_CLOSED'));
    }

	/**
	 * check si on peut entrer dans un projet en écriture.
	 *
	 * @param Project $project
	 * @param UserInterface $user
	 * @return bool
	 */
	private function canWrite(Project $project, UserInterface $user): bool
	{
		return $project->hasUser($user) && (null === $project->getClosedAt());
	}

    /**
     * check si on peut entrer dans un projet en écriture.
     */
    private function canAccessAndOpen(Project $project, UserInterface $user): bool
    {
        return $project->hasUser($user) && null === $project->getClosedAt();
    }
}
