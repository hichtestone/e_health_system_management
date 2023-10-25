<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\AdverseEventAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdverseEventAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdverseEventAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdverseEventAuditTrail[]    findAll()
 * @method AdverseEventAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdverseEventAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdverseEventAuditTrail::class);
    }

    // /**
    //  * @return AdverseEventAuditTrail[] Returns an array of AdverseEventAuditTrail objects
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
    public function findOneBySomeField($value): ?AdverseEventAuditTrail
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
