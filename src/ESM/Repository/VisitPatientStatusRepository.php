<?php

namespace App\ESM\Repository;

use App\ESM\Entity\VisitPatientStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitPatientStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitPatientStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitPatientStatus[]    findAll()
 * @method VisitPatientStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitPatientStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitPatientStatus::class);
    }

    public function getVisitPatientStatus()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT visit_patient_status.id as id, visit_patient_status.label as label
            FROM visit_patient_status
            WHERE visit_patient_status.id  = 1 or visit_patient_status.id  = 2';
        $stmt = $conn->prepare($sql);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }



}
