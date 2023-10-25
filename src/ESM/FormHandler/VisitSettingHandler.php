<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Patient;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\VariableType;
use App\ESM\Entity\Visit;
use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use App\ESM\Form\VisitSettingType;
use App\ESM\Service\Utils\MonitoringDateAndStatus;
use Doctrine\ORM\EntityManagerInterface;

class VisitSettingHandler extends AbstractFormHandler
{
    private $entityManager;
    private $monitoringDateAndStatus;

    public function __construct(EntityManagerInterface $entityManager, MonitoringDateAndStatus $monitoringDateAndStatus)
    {
        $this->entityManager = $entityManager;
        $this->monitoringDateAndStatus = $monitoringDateAndStatus;
    }

    protected function getFormType(): string
    {
        return VisitSettingType::class;
    }

	/**
	 * @param Visit $data
	 * @throws \Exception
	 */
    protected function process($data): void
    {
        //$delay = null === $data->getDelay() ? 0 : (int) $data->getDelay();
        $delay = $data->getDelay();
       // $delay_approx = null === $data->getDelayApprox() ? 0 : (int) $data->getDelayApprox();
        $delay_approx =  $data->getDelayApprox();

        if (null === $data->getId()) {
            // Type de variable = date
            $variableType = $this->entityManager->getRepository(VariableType::class)->findOneBy(['label' => 'date']);

            $patientVariable = new PatientVariable();
            $patientVariable->setProject($data->getProject());
            $patientVariable->setLabel($data->getShort());
            $patientVariable->setVariableType($variableType);
            $patientVariable->setIsVisit(true);
            $this->entityManager->persist($patientVariable);

            $data->setDelay($delay);
            $data->setDelayApprox($delay_approx);
            $data->addVariable($patientVariable);

			// Ajout Exam
			$patientVariable->setVisit($data);

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            // On rajoute la nouvelle variable visit pour tous le patient s'elle existe
            $visitPatient = $this->entityManager->getRepository(VisitPatient::class)->findBy(['visit' => $data]);
            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
            if (empty($visitPatient)) {
                // pour chaque patient
                $patients = $this->entityManager->getRepository(Patient::class)->findBy(['project' => $data->getProject()]);
                if ($patients) {
                    foreach ($patients as $patient) {
                        $entity = new VisitPatient();
                        $entity->setPatient($patient);
                        $entity->setVisit($data);
                        $entity->setVariable($patientVariable);
                        $entity->setStatus($status);
                        $entity->setBadge('');
                        $entity->setIteration(1);
                        $entity->setSourceId('');
                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();

                        $patientData = new PatientData();
                        $patientData->setPatient($patient);
                        $patientData->setVariable($patientVariable);
                        $patientData->setVariableValue('');
                        $patientData->setOrdre(1);
                        $patientData->setDisabledAt(new \DateTime());
                        $this->entityManager->persist($patientData);

                        $this->entityManager->flush();

                        // Calcul dates & statuts
                        // $patientData = $this->entityManager->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $patientVariable]);

                        if (isset($patientData)) {
                            // METTRE à JOUR Liste STATUT VISIT
                            $params = [
                                'data' => [
                                    'idPatient' => $patient->getId(),
                                    'patient' => $patient->getNumber(),
                                    'center' => $patient->getCenter()->getNumber(),
                                    'variables' => [
                                        $patientData->getVariable()->getLabel() => ['2', $patientData->getVariableValue()],
                                    ],
                                ],
                            ];

                            foreach ($params['data']['variables'] as $key => $variable) {
                                $this->monitoringDateAndStatus->monitoringDateAndStatusVisit($data->getProject(), $params['data'], $variable['1'], $key);
                            }
                        }
                    }
                }
            }

            //  $this->entityManager->flush();
        } else {
            $data->setDelay($delay);
            $data->setDelayApprox($delay_approx);
            // update label (patientVariable)
            $idPatientVariable = $this->entityManager->getRepository(Visit::class)->getPatientVariableByVisit($data->getId());
            if ($idPatientVariable) {
                $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
                $patientVariable->setLabel($data->getShort());
            }
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            // Calcule statut et dates avec la nouvelle réference

            $patients = $this->entityManager->getRepository(Patient::class)->findBy(['project' => $data->getProject()]);
            foreach ($patients as $patient) {
                $patientData = $this->entityManager->getRepository(PatientData::class)->findOneBy(['patient' => $patient, 'variable' => $data->getPatientVariable()]);

                if (isset($patientData)) {
                    // METTRE à JOUR Liste STATUT VISIT
                    $params = [
                         'data' => [
                             'idPatient' => $patient->getId(),
                             'patient' => $patient->getNumber(),
                             'center' => $patient->getCenter()->getNumber(),
                             'variables' => [
                                 $patientData->getVariable()->getLabel() => ['2', $patientData->getVariableValue()],
                             ],
                         ],
                     ];

                    foreach ($params['data']['variables'] as $key => $variable) {
                        $this->monitoringDateAndStatus->monitoringDateAndStatusVisit($data->getProject(), $params['data'], $variable['1'], $key);
                    }
                }
            }
        }

        // Set Position
        $result = $this->entityManager->getRepository(Visit::class)->findOneBy([], ['id' => 'DESC'], 1, 0);
        $data->setPosition($result->getId());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

}
