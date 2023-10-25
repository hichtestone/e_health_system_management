<?php

namespace App\ESM\Repository\DropdownList;

use App\ESM\Entity\DropdownList\SpecialtyOne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecialtyOne|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialtyOne|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialtyOne[]    findAll()
 * @method SpecialtyOne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialtyOneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialtyOne::class);
    }

    // /**
    //  * @return SpecialtyOne[] Returns an array of SpecialtyOne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SpecialtyOne
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
