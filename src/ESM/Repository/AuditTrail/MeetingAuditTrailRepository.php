<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\MeetingAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingAuditTrail[]    findAll()
 * @method MeetingAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingAuditTrail::class);
    }

    // /**
    //  * @return MeetingAuditTrail[] Returns an array of MeetingAuditTrail objects
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
    public function findOneBySomeField($value): ?MeetingAuditTrail
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
