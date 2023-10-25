<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\PatientData;
use App\ESM\Entity\Visit;
use App\ESM\Entity\VisitPatient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class VisitSettingVoter extends Voter
{
    const LIST = 'VISITSETTING_LIST';
    const SHOW = 'VISITSETTING_SHOW';
    const EDIT = 'VISITSETTING_EDIT';
    const CREATE = 'VISITSETTING_CREATE';
    const ARCHIVE = 'VISITSETTING_ARCHIVE';
    const RESTORE = 'VISITSETTING_RESTORE';

    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::LIST, self::CREATE], true) ||
            (in_array($attribute, [self::SHOW, self::EDIT, self::ARCHIVE, self::RESTORE], true)
                && $subject instanceof Visit);
    }

    protected function voteOnAttribute($attribute, $visit, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($visit);

            case self::EDIT:
                return $this->canEdit($visit);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($visit);

            case self::RESTORE:
                return $this->canRestore($visit);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canShow(Visit $visit): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ')
			&& $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $visit->getProject());
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ') && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');
    }

    private function canEdit(Visit $visit): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $visit->getProject());
    }

    private function canArchive(Visit $visit): bool
    {
        // check si présent dans une visit non clos = impossible à archiver
        if ($this->security->isGranted('ROLE_DIAGRAMVISIT_READ')
            && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && null === $visit->getDeletedAt()) {

            $idPatientVariable = $this->entityManager->getRepository(Visit::class)->getPatientVariableByVisit($visit->getId());
            $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($visit->getProject()->getId());
            foreach ($idsPatientData as $idPatientData) {
                if ($idPatientVariable === $idPatientData['idVariable']) {
                    if ('' !== $idPatientData['value']) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    private function canRestore(Visit $visit): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $visit->getProject())
            && null !== $visit->getDeletedAt();
    }
}
