<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\VisitPatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitPatient|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitPatient|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitPatient[]    findAll()
 * @method VisitPatient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitPatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitPatient::class);
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
    public function indexListGen(Project $project): QueryBuilder
	{
        return $this->createQueryBuilder('vp');
    }

	/**
	 * @param $projectId
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function getVisitPatient($projectId): array
	{
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
            visit_patient.id as id, patient.number as patient, center.number as center, visit.short as visit, phase_setting.label as phase, visit_patient_status.id as status, visit_patient_status.label as statusLabel, visit_patient_status.label as label,
            visit_patient.occured_at as occuredAt, visit_patient.monitored_at as monitoredAt, visit_patient.variable_id as reel, visit.patient_variable_id as ref,  visit_patient.badge as badge, visit.delay as delay, visit.delay_approx as approx
            FROM visit_patient
            INNER JOIN patient ON visit_patient.patient_id = patient.id 
            INNER JOIN center ON patient.center_id = center.id 
            INNER JOIN visit ON visit_patient.visit_id = visit.id
            INNER JOIN patient_variable ON visit_patient.variable_id = patient_variable.id
            INNER JOIN visit_patient_status ON visit_patient.status_id = visit_patient_status.id
            INNER JOIN phase_setting ON visit.phase_id = phase_setting.id
            
            -- Prise en compte du conditionnement
            LEFT JOIN condition_visit_patient ON condition_visit_patient.visit_patient_id = visit_patient.id
            
            WHERE patient.project_id = :projectId  AND patient_variable.has_visit = 1 AND visit_patient.deleted_at IS NULL
            
            -- Prise en compte du conditionnement
            AND condition_visit_patient.id IS NULL
            
            ORDER BY center.number ASC, patient.number ASC, visit.position ASC;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getVariableValue($patient, $visit, $project)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
            patient_data.variable_value as val, patient_variable.label as ref
            FROM visit_patient 
            INNER JOIN visit ON visit_patient.visit_id = visit.id 
            INNER JOIN patient_variable ON visit.patient_variable_id = patient_variable.id 
            INNER JOIN patient_data ON patient_variable.id = patient_data.variable_id 
            INNER JOIN patient ON patient_data.patient_id = patient.id 
            WHERE visit_patient.id = :visit and patient.id = :patient and patient.project_id = :project';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['patient' => $patient, 'visit' => $visit, 'project' => $project]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetch();
    }

    public function getVariableValueVisit($patient, $visit, $project)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
            DISTINCT(patient_data.variable_value) as val, patient_variable.label as ref
            FROM visit_patient 
            INNER JOIN visit ON visit_patient.visit_id = visit.id 
            INNER JOIN patient_variable ON visit.patient_variable_id = patient_variable.id 
            INNER JOIN patient_data ON patient_variable.id = patient_data.variable_id 
            INNER JOIN patient ON patient_data.patient_id = patient.id 
            WHERE visit.id = :visit and patient.id = :patient and patient.project_id = :project';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['patient' => $patient, 'visit' => $visit, 'project' => $project]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetch();
    }

    public function getVisitPatientByPatient($patient, $center)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT visit_patient.id as id, visit_patient.patient_id as patient, center.id as center, visit_patient.visit_id as visit, visit_patient.variable_id as variable, visit_patient.status_id as status, visit_patient.occured_at as occuredAt,
            visit_patient.monitored_at as monitoredAt, visit_patient.badge as badge, visit.short as label
            FROM `visit_patient`
            INNER JOIN visit ON visit_patient.visit_id = visit.id
            INNER JOIN patient ON visit_patient.patient_id = patient.id
            INNER JOIN center ON patient.center_id = center.id
            WHERE visit_patient.patient_id = :patient AND center.number = :center;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['patient' => $patient, 'center' => $center]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getStatutPatient($projectId, $visitPatient)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
            visit_patient.id as id, patient.number as patient, center.number as center, visit.short as visit, phase_setting.label as phase, visit_patient_status.id as status, visit_patient_status.label as label,
            visit_patient.occured_at as occuredAt, visit_patient.monitored_at as monitoredAt, visit_patient.variable_id as reel, visit.patient_variable_id as ref,  visit_patient.badge as badge, visit.delay as delay, visit.delay_approx as approx
            FROM visit_patient
            INNER JOIN patient ON visit_patient.patient_id = patient.id 
            INNER JOIN center ON patient.center_id = center.id 
            INNER JOIN visit ON visit_patient.visit_id = visit.id
            INNER JOIN patient_variable ON visit_patient.variable_id = patient_variable.id
            INNER JOIN visit_patient_status ON visit_patient.status_id = visit_patient_status.id
            INNER JOIN phase_setting ON visit.phase_id = phase_setting.id
            WHERE patient.project_id = :projectId AND patient_variable.has_visit = 1 AND visit_patient.deleted_at IS NULL AND visit_patient.id = :visitPatient;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'visitPatient' => $visitPatient]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getStatusByPatientAndVariable($projectId, $patient, $variable)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
            visit_patient_status.id as status
            FROM visit_patient
            INNER JOIN patient ON visit_patient.patient_id = patient.id 
            INNER JOIN patient_variable ON visit_patient.variable_id = patient_variable.id
            INNER JOIN visit_patient_status ON visit_patient.status_id = visit_patient_status.id
            WHERE patient.project_id = :projectId AND patient.id = :patient AND patient_variable.id = :variable ;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'patient' => $patient, 'variable' => $variable]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }
}
