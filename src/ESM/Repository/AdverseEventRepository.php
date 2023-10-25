<?php

namespace App\ESM\Repository;

use App\ESM\Entity\AdverseEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdverseEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdverseEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdverseEvent[]    findAll()
 * @method AdverseEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdverseEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdverseEvent::class);
    }

    // /**
    //  * @return AdverseEvent[] Returns an array of AdverseEvent objects
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
    public function findOneBySomeField($value): ?AdverseEvent
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
