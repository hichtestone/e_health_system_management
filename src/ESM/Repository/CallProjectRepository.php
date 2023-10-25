<?php

namespace App\ESM\Repository;

use App\ESM\Entity\CallProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallProject[]    findAll()
 * @method CallProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallProject::class);
    }

    // /**
    //  * @return CallProject[] Returns an array of CallProject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CallProject
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
