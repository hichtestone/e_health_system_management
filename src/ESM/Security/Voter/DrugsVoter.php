<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Drug;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DrugsVoter extends Voter
{
    const LIST = 'DRUG_LIST';
    const SHOW = 'DRUG_SHOW';
    const EDIT = 'DRUG_EDIT';
    const CREATE = 'DRUG_CREATE';
    const ARCHIVE = 'DRUG_ARCHIVE';
    const RESTORE = 'DRUG_RESTORE';

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
        return in_array($attribute, [self::LIST, self::CREATE, self::SHOW, self::EDIT, self::ARCHIVE, self::RESTORE], true
                && $subject instanceof Drug);
    }

    protected function voteOnAttribute($attribute, $drug, TokenInterface $token)
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($drug);

            case self::EDIT:
                return $this->canEdit($drug);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($drug);
            case self::RESTORE:
                return $this->canRestore($drug);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DRUG_READ');
    }

    private function canShow(Drug $drug): bool
    {
        return $this->security->isGranted('ROLE_DRUG_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DRUG_WRITE');
    }

    private function canEdit(Drug $drug): bool
    {
        return $this->security->isGranted('ROLE_DRUG_WRITE');
    }

    /*private function canArchive($drug)
    {
        return $this->security->isGranted('ROLE_DRUG_ARCHIVE') && null === $drug->getDeletedAt();
    }*/
    private function canRestore(Drug $subject): bool
    {
        return $this->security->isGranted('ROLE_DRUG_ARCHIVE')
            && null !== $subject->getDeletedAt();
    }

    private function canArchive(Drug $subject): bool
    {
        // check si présent dans un projet non clos = impossible à archiver
        if ($this->security->isGranted('ROLE_DRUG_ARCHIVE')
            && null === $subject->getDeletedAt()) {
            $projects = $subject->getProjects();
            foreach ($projects as $project) {
                if (null === $project->getClosedAt()) {
                    return false;
                }
            }
        }

        return true;
    }
}
