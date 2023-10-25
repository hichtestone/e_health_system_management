<?php

namespace App\ESM\Message;

use App\ESM\Entity\ConditionVisitPatient;
use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Entity\Visit;
use App\ESM\Entity\VisitPatient;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\ESM\Service\Study\SchemaCondition as SchemaConditionService;

/**
 * Class ConditionMessageHandler
 * @package App\Message
 */
class ConditionMessageHandler implements MessageHandlerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SchemaConditionService
     */
    private $schemaCondition;

    public function __construct(ManagerRegistry $managerRegistry, LoggerInterface $logger, SchemaConditionService $schemaCondition)
    {
        $this->managerRegistry = $managerRegistry;
        $this->logger = $logger;
        $this->schemaCondition = $schemaCondition;
    }

    public function __invoke(ConditionMessage $condition): void
    {
        // Id dans SchemaConditionMessage.php
        $condition_id = $condition->getConditionId();

        /** @var SchemaCondition $conditionEntity */
        $conditionEntity = $this->managerRegistry->getRepository(SchemaCondition::class)->find($condition_id);


        // Supprimer lignes avant d'ajouter de nouvelles
        if (!empty($conditionEntity->getConditionVisitPatients())) {
            foreach ($conditionEntity->getConditionVisitPatients() as $conditionVisitPatient) {
                $this->managerRegistry->getManager()->remove($conditionVisitPatient);
            }

            // Ecrire dans la BDD
            $this->managerRegistry->getManager()->flush();
        }

        // Ne rien faire si la condition est desactivée
        if (null != $conditionEntity && $conditionEntity->getDisabled()) {
            return;
        }


        // Liste des patients ID qui entrent
        // dans les conditions paramétrées par la condition en cours
        $patients_id = $this->schemaCondition->getPatients($conditionEntity);

        //dump($patients_id);

        // Patient inexistants
        if (empty($patients_id)) {
            return;
        }

        // Condition liée à une visite
        $is_visit_linked = null != $conditionEntity->getVisit();

        // Condition liée à une phase
        $is_phase_linked = null != $conditionEntity->getPhase();

        foreach ($patients_id as $patient_id) {
            // Lier à une visite
            if ($is_visit_linked) {
                $this->add_visit_patient($patient_id, $conditionEntity->getVisit(), $conditionEntity);
                $this->logger->info('patient: '.$patient_id);
            }

            // Lier à une phase
            if ($is_phase_linked) {
                $this->add_phase_patient($patient_id, $conditionEntity->getPhase(), $conditionEntity);
            }
        }

        // Ecrire dans la BDD
        $this->managerRegistry->getManager()->flush();
    }

    private function add_visit_patient(int $patient_id, Visit $visite, SchemaCondition $conditionEntity): void
    {
        /** @var VisitPatient $visit_patient */
        $visit_patient = $this->managerRegistry->getRepository(VisitPatient::class)->findOneBy([
            'patient' => $patient_id,
            'visit' => $visite,
        ]);

        if (null != $visit_patient) {
            $conditionVisitPatient = new ConditionVisitPatient();
            $conditionVisitPatient->setExecutedAt(new \DateTime());
            $conditionVisitPatient->setSchemaCondition($conditionEntity);
            $conditionVisitPatient->setVisitPatient($visit_patient);
            $this->managerRegistry->getManager()->persist($conditionVisitPatient);
        }
    }

    private function add_phase_patient($patient_id, PhaseSetting $phase, SchemaCondition $conditionEntity): void
    {
        // Visites dans la phase
        $visits = $this->managerRegistry->getRepository(Visit::class)->findBy([
            'phase' => $phase,
        ]);

        // Pour chaque visite
        if (null != $visits) {
            foreach ($visits as $visit) {
                $this->add_visit_patient($patient_id, $visit, $conditionEntity);
            }
        }
    }
}
