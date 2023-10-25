<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\PhaseSetting;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PhaseSettingVoter extends Voter
{
    const LIST = 'PHASESETTING_LIST';
    const EDIT = 'PHASESETTING_EDIT';
    const CREATE = 'PHASESETTING_CREATE';
    const ARCHIVE = 'PHASESETTING_ARCHIVE';
    const RESTORE = 'PHASESETTING_RESTORE';

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
                && $subject instanceof PhaseSetting);
    }

    protected function voteOnAttribute($attribute, $phaseSetting, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::EDIT:
                return $this->canEdit($phaseSetting);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($phaseSetting);

            case self::RESTORE:
                return $this->canRestore($phaseSetting);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ') && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');
    }

    private function canEdit(PhaseSetting $phaseSetting): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $phaseSetting->getProject());
    }

    private function canArchive(PhaseSetting $phaseSetting): bool
    {
        // check si présent dans une phase non clos = impossible à archiver
        if ($this->security->isGranted('ROLE_DIAGRAMVISIT_READ')
            && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && null === $phaseSetting->getDeletedAt()) {
            $visits = $phaseSetting->getVisits();
            foreach ($visits as $visit) {
                if (null === $visit->getDeletedAt()) {
                    if (null === $visit->getProject()->getClosedAt()) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    private function canRestore(PhaseSetting $phaseSetting): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ')
            && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $phaseSetting->getProject())
            && null !== $phaseSetting->getDeletedAt();
    }
}
