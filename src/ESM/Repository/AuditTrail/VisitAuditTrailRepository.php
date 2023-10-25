<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\VisitAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitAuditTrail[]    findAll()
 * @method VisitAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitAuditTrail::class);
    }

    // /**
    //  * @return VisitAuditTrail[] Returns an array of VisitAuditTrail objects
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
    public function findOneBySomeField($value): ?VisitAuditTrail
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
