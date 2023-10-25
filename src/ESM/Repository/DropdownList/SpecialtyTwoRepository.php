<?php

namespace App\ESM\Repository\DropdownList;

use App\ESM\Entity\DropdownList\SpecialtyTwo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecialtyTwo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialtyTwo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialtyTwo[]    findAll()
 * @method SpecialtyTwo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialtyTwoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialtyTwo::class);
    }

    // /**
    //  * @return SpecialtyTwo[] Returns an array of SpecialtyTwo objects
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
    public function findOneBySomeField($value): ?SpecialtyTwo
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
