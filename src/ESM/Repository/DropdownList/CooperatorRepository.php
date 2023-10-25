<?php

namespace App\ESM\Repository\DropdownList;

use App\ESM\Entity\DropdownList\Cooperator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cooperator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cooperator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cooperator[]    findAll()
 * @method Cooperator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CooperatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cooperator::class);
    }

    // /**
    //  * @return Cooperator[] Returns an array of Cooperator objects
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
    public function findOneBySomeField($value): ?Cooperator
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
