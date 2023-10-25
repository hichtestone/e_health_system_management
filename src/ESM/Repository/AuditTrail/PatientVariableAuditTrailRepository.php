<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PatientVariableAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientVariableAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientVariableAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientVariableAuditTrail[]    findAll()
 * @method PatientVariableAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientVariableAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientVariableAuditTrail::class);
    }

    // /**
    //  * @return PatientVariableAuditTrail[] Returns an array of PatientVariableAuditTrail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PatientVariableAuditTrail
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
