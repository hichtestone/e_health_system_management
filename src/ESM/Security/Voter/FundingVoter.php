<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Funding;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FundingVoter extends Voter
{
    const LIST = 'FUNDING_LIST';
    const SHOW = 'FUNDING_SHOW';
    const EDIT = 'FUNDING_EDIT';
    const CREATE = 'FUNDING_CREATE';
    const ARCHIVE = 'FUNDING_ARCHIVE';
    const RESTORE = 'FUNDING_RESTORE';

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
                && $subject instanceof Funding);
    }

    protected function voteOnAttribute($attribute, $funding, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($funding);

            case self::EDIT:
                return $this->canEdit($funding);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($funding);

            case self::RESTORE:
                return $this->canRestore($funding);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_READ');
    }

    private function canShow(Funding $funding): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_WRITE');
    }

    private function canEdit(Funding $funding): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $funding->getProject())
			&& null === $funding->getDeletedAt();
    }

    private function canArchive(Funding $funding): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_ARCHIVE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $funding->getProject())
            && null === $funding->getDeletedAt();
    }

    private function canRestore(Funding $funding): bool
    {
        return $this->security->isGranted('ROLE_FUNDING_ARCHIVE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $funding->getProject())
            && null !== $funding->getDeletedAt();
    }
}
