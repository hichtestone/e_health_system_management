<?php

namespace App\ESM\Security\Voter;

use App\ESM\Entity\Exam;
use App\ESM\Entity\PatientData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ExamSettingVoter extends Voter
{
    const LIST = 'EXAMSETTING_LIST';
    const SHOW = 'EXAMSETTING_SHOW';
    const EDIT = 'EXAMSETTING_EDIT';
    const CREATE = 'EXAMSETTING_CREATE';
    const ARCHIVE = 'EXAMSETTING_ARCHIVE';
    const RESTORE = 'EXAMSETTING_RESTORE';

    /**
     * @var Security
     */
    private $security;
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
                && $subject instanceof Exam);
    }

    protected function voteOnAttribute($attribute, $exam, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        switch ($attribute) {
            case self::LIST:
                return $this->canList();

            case self::SHOW:
                return $this->canShow($exam);

            case self::EDIT:
                return $this->canEdit($exam);

            case self::CREATE:
                return $this->canCreate();

            case self::ARCHIVE:
                return $this->canArchive($exam);

            case self::RESTORE:
                return $this->canRestore($exam);
        }

        return false;
    }

    private function canList(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canShow(Exam $exam): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ');
    }

    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_READ') && $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');
    }

    private function canEdit(Exam $exam): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $exam->getProject());
    }

    private function canArchive(Exam $exam): bool
    {
        if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $exam->getProject())
            && null === $exam->getDeletedAt()) {

            $idPatientVariable = $this->entityManager->getRepository(Exam::class)->getPatientVariableByExam($exam->getId());

            $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($exam->getProject()->getId());

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

    private function canRestore(Exam $exam): bool
    {
        return $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')
            && $this->security->isGranted('PROJECT_ACCESS_AND_OPEN', $exam->getProject())
            && null !== $exam->getDeletedAt();
    }
}
