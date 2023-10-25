<?php

namespace App\ESM\Repository;

use App\ESM\Entity\TrailType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrailType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrailType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrailType[]    findAll()
 * @method TrailType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrailTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrailType::class);
    }

    // /**
    //  * @return TrailType[] Returns an array of TrailType objects
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
    public function findOneBySomeField($value): ?TrailType
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
