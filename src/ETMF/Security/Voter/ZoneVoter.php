<?php

namespace App\ETMF\Security\Voter;

use App\ETMF\Entity\Zone;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ZoneVoter extends Voter
{
    public const LIST 	 = 'ZONE_LIST';
    public const EDIT    = 'ZONE_EDIT';
    public const CREATE  = 'ZONE_CREATE';
    public const ARCHIVE = 'ZONE_ARCHIVE';
    public const RESTORE = 'ZONE_RESTORE';

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
                && $subject instanceof Zone);
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
	 * @param Zone $zone
	 * @return bool
	 */
    private function canEdit(Zone $zone): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
            && $zone->getDeletedAt() === null;
    }

	/**
	 * @param Zone $zone
	 * @return bool
	 */
    private function canArchive(Zone $zone): bool
    {
		return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
			&& null === $zone->getDeletedAt();
    }

	/**
	 * @param Zone $zone
	 * @return bool
	 */
    private function canRestore(Zone $zone): bool
    {
        return $this->security->isGranted('ROLE_ETMF_FULL_ACCESS') && $this->security->isGranted('ROLE_ETMF_WRITE')
            && null !== $zone->getDeletedAt();
    }
}
