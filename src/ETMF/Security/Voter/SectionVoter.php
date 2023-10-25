<?php

namespace App\ETMF\Security\Voter;

use App\ETMF\Entity\Section;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SectionVoter extends Voter
{
    public const LIST 	 = 'SECTION_LIST';
    public const EDIT    = 'SECTION_EDIT';
    public const CREATE  = 'SECTION_CREATE';
    public const ARCHIVE = 'SECTION_ARCHIVE';
    public const RESTORE = 'SECTION_RESTORE';

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
        return in_array($attribute, [self::LIST, self::CREATE], true) || (in_array($attribute, [self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof Section);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {

            case self::LIST:
                return $this->canList();

            case self::EDIT:
                return $this->canEdit($subject);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($subject);

            case self::RESTORE:
                return $this->canRestore($subject);
        }

        return false;
    }

	/**
	 * @return bool
	 */
    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_READ');
    }

	/**
	 * @return bool
	 */
    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE');
    }

	/**
	 * @param Section $section
	 * @return bool
	 */
    private function canEdit(Section $section): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
            && $section->getDeletedAt() === null;
    }

	/**
	 * @param Section $section
	 * @return bool
	 */
    private function canArchive(Section $section): bool
    {
		return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
			&& null === $section->getDeletedAt();
    }

	/**
	 * @param Section $section
	 * @return bool
	 */
    private function canRestore(Section $section): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
            && null !== $section->getDeletedAt();
    }
}
