<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Patient;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(Project $project)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.center', 'c')
            ->where('p.project = :project')
            ->setParameter('project', $project);
    }

    public function findByDateInclusion($courbe)
    {
        $results = $this->getEntityManager()->createQueryBuilder('p')
            ->select('count(p.id),SUBSTRING(p.inclusionAt, 1, 10) as date')
            ->from(Patient::class, 'p')
            ->groupBy('date')
            ->getQuery()->getResult();

        return $results;
    }

    public function countPatientCenter($projectId, $patient, $center)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient.id as idPatient, count(patient.id) as counter  FROM `patient`
            INNER JOIN center ON patient.center_id = center.id
            WHERE patient.project_id = :projectId AND patient.number = :patient AND center.number = :center;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId, 'patient' => $patient, 'center' => $center]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }
}
