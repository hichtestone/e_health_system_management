<?php

namespace App\ETMF\Security\Voter;

use App\ETMF\Entity\Tag;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TagVoter extends Voter
{
	public const LIST 	 = 'TAG_LIST';
	public const EDIT    = 'TAG_EDIT';
	public const CREATE  = 'TAG_CREATE';
	public const ARCHIVE = 'TAG_ARCHIVE';
	public const RESTORE = 'TAG_RESTORE';

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
			(in_array($attribute, [self::EDIT, self::ARCHIVE, self::RESTORE], true)
				&& $subject instanceof Tag);
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
	 * @param Tag $tag
	 * @return bool
	 */
	private function canEdit(Tag $tag): bool
	{
		return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
			&& null === $tag->getDeletedAt();
	}

	/**
	 * @param Tag $tag
	 * @return bool
	 */
	private function canArchive(Tag $tag): bool
	{
		if ($this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
			&& null === $tag->getDeletedAt()) {

			return true;
		}

		return false;
	}

	/**
	 * @param Tag $tag
	 * @return bool
	 */
	private function canRestore(Tag $tag): bool
	{
		return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
			&& null !== $tag->getDeletedAt();
	}
}
