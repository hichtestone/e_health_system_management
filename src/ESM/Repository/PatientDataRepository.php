<?php

namespace App\ESM\Repository;

use App\ESM\Entity\PatientData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientData|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientData|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientData[]    findAll()
 * @method PatientData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientData::class);
    }

	/**
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function getPatientData($projectId): array
	{
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_data.ordre as ordre, patient_data.id as id, patient_variable.visit_id as visit, patient_variable.exam_id as exam, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable, phase_setting_status.label as phase_label 
            FROM patient_data
            INNER JOIN patient ON patient_data.patient_id = patient.id 
            INNER JOIN center ON patient.center_id = center.id
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id 
            LEFT JOIN visit_variable ON patient_variable.id = visit_variable.patient_variable_id
            LEFT JOIN visit_patient ON patient.id = visit_patient.patient_id
			LEFT JOIN visit ON visit.id = visit_patient.visit_id
     		LEFT JOIN phase_setting ON phase_setting.id = visit.phase_id
            LEFT JOIN phase_setting_status ON phase_setting.phase_setting_status_id = phase_setting_status.id
            LEFT JOIN exam_variable  ON patient_variable.id = exam_variable .patient_variable_id
            LEFT JOIN exam ON exam.id = exam_variable .exam_id
            WHERE patient.project_id = :projectId and patient_variable.deleted_at is null and patient_data.disabled_at is null and patient_variable.has_patient = 1
            ORDER BY patient_data.ordre ASC, visit.position ASC, exam.position ASC, center.number ASC, patient.number ASC, patient_variable.id ASC
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getAllPatientData($projectId): array
	{
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_data.id as id, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable 
            FROM patient_data
            INNER JOIN patient ON patient_data.patient_id = patient.id 
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id 
            INNER JOIN center ON patient.center_id = center.id
            WHERE patient.project_id = :projectId;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getList($projectId, $patient, $center)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT variable_option.label, variable_option.code FROM `patient_data`
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
            INNER JOIN patient ON patient_data.patient_id = patient.id
            INNER JOIN center ON patient.center_id = center.id
            INNER JOIN variable_list ON patient_variable.variable_list_id = variable_list.id
            INNER JOIN variable_option ON variable_list.id = variable_option.list_id
            WHERE patient.number = :patient AND center.number = :center AND patient.project_id = :projectId;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'center' => $center, 'patient' => $patient]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getPatientVariableByPatientCenter($projectId, $patient, $center, $variable)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_data.id as id FROM `patient_data`
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
            INNER JOIN patient ON patient_data.patient_id = patient.id
            INNER JOIN center ON patient.center_id = center.id
            WHERE patient.number = :patient 
            AND center.number = :center 
            AND patient.project_id = :projectId 
            AND patient_variable.label = :variable;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'projectId' => $projectId,
            'center' => $center,
            'patient' => $patient,
            'variable' => $variable,
        ]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    public function getPatientVariableByPatientVariable($projectId, $patient, $variable)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_data.id as id FROM `patient_data`
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
            INNER JOIN patient ON patient_data.patient_id = patient.id
            WHERE patient.id = :patient 
            AND patient.project_id = :projectId 
            AND patient_variable.label = :variable;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'projectId' => $projectId,
            'patient' => $patient,
            'variable' => $variable,
        ]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    public function getVariable($projectId, $patient, $variable)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_data.variable_value as val  FROM `patient_data`
            INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
            INNER JOIN patient ON patient_data.patient_id = patient.id
            WHERE patient_data.patient_id = :patient AND patient_data.variable_id = :variable AND patient.project_id = :projectId;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'patient' => $patient, 'variable' => $variable]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    public function getListOption($projectId, $idVariableList)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT variable_option.label, variable_option.code FROM `patient_variable`
            INNER JOIN variable_list ON patient_variable.variable_list_id = variable_list.id
            INNER JOIN variable_option ON variable_list.id = variable_option.list_id
            WHERE patient_variable.id = :idList AND patient_variable.project_id = :projectId;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'idList' => $idVariableList]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getListOptionLabelCode($projectId, $idVariableList, $code)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT variable_option.label as label FROM `patient_variable`
            INNER JOIN variable_list ON patient_variable.variable_list_id = variable_list.id
            INNER JOIN variable_option ON variable_list.id = variable_option.list_id
            WHERE patient_variable.id = :idList AND patient_variable.project_id = :projectId AND variable_option.code = :code;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'idList' => $idVariableList, 'code' => $code]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

}
