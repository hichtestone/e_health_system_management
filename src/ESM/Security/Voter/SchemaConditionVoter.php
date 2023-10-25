<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\SchemaCondition;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SchemaConditionVoter extends Voter
{
    const LIST = 'SCHEMACONDITION_LIST';
    const SHOW = 'SCHEMACONDITION_SHOW';
    const EDIT = 'SCHEMACONDITION_EDIT';
    const CREATE = 'SCHEMACONDITION_CREATE';
    const ARCHIVE = 'SCHEMACONDITION_ARCHIVE';

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
            (in_array($attribute, [self::SHOW, self::EDIT, self::ARCHIVE], true)
                && $subject instanceof SchemaCondition);
    }

    protected function voteOnAttribute($attribute, $schemaCondition, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($schemaCondition);

            case self::EDIT:
                return $this->canEdit($schemaCondition);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($schemaCondition);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canShow(SchemaCondition $schemaCondition): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ')
            && null === $schemaCondition->getDeletedAt();
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');
    }

    private function canEdit(SchemaCondition $schemaCondition): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $schemaCondition->getProject())
            && null === $schemaCondition->getDeletedAt();
    }

    private function canArchive(SchemaCondition $schemaCondition): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $schemaCondition->getProject())
            && null === $schemaCondition->getDeletedAt();
    }
}
