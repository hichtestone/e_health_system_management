<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Exam;
use App\ESM\Entity\ExamPatientVariable;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\VariableType;
use App\ESM\Form\ExamType;
use Doctrine\ORM\EntityManagerInterface;

class ExamSettingHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return ExamType::class;
    }

    /**
     * @param Exam $data
     */
    protected function process($data): void
    {
        if (null === $data->getId()) {
            // Type de variable = date
            $variableType = $this->entityManager->getRepository(VariableType::class)->findOneBy(['label' => 'date']);

            $variable = new PatientVariable();
            $variable->setProject($data->getProject());
            $variable->setLabel($data->getName());
            $variable->setVariableType($variableType);
            $variable->setIsExam(true);

            // Ajout Exam
            $variable->setExam($data);

            $this->entityManager->persist($variable);
			$data->addVariable($variable);

			$this->entityManager->persist($data);

            $this->entityManager->flush();
        } else {
            // update label (patientVariable)
            $idPatientVariable = $this->entityManager->getRepository(Exam::class)->getPatientVariableByExam($data->getId());
            if ($idPatientVariable) {
                $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
                $patientVariable->setLabel($data->getName());
            }
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        // Set Position
        $result = $this->entityManager->getRepository(Exam::class)->findOneBy([], ['id' => 'DESC'], 1, 0);
        $data->setPosition($result->getId());
        $this->entityManager->persist($data);

        $this->entityManager->flush();
    }
}
