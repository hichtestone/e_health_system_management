<?php

namespace App\ESM\Repository;

use App\ESM\Entity\TrailPhase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrailPhase|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrailPhase|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrailPhase[]    findAll()
 * @method TrailPhase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrailPhaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrailPhase::class);
    }

    // /**
    //  * @return TrailPhase[] Returns an array of TrailPhase objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrailPhase
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
