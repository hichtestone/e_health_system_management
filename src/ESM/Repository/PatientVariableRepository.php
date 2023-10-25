<?php

namespace App\ESM\Repository;

use App\ESM\Entity\PatientVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientVariable|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientVariable|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientVariable[]    findAll()
 * @method PatientVariable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientVariable::class);
    }

    public function getVariableByList($label)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT variable_list.label as name, variable_option.label as label, variable_option.code as value
            FROM variable_option
            INNER JOIN variable_list ON variable_option.list_id = variable_list.id 
            INNER JOIN patient_variable ON variable_list.patient_id = patient_variable.id 
            WHERE patient_variable.label  = :variableId;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['variableId' => $label]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function getOptionByVariableId($idVariable)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT variable_option.label, variable_option.code FROM variable_option INNER JOIN variable_list ON variable_option.list_id = variable_list.id INNER JOIN patient_variable ON variable_list.patient_id = patient_variable.id
            WHERE patient_variable.id  = :idVariable;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idVariable' => $idVariable]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function countPatientVariable($projectId, $label)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT count(patient_variable.id) as counter
            FROM `patient_variable`
            WHERE patient_variable.project_id = :projectId 
              AND patient_variable.label = :label 
              AND patient_variable.deleted_at IS NULL;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'label' => $label]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }
}
