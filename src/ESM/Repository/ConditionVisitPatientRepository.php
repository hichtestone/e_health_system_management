<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ConditionVisitPatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConditionVisitPatient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConditionVisitPatient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConditionVisitPatient[]    findAll()
 * @method ConditionVisitPatient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionVisitPatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConditionVisitPatient::class);
    }

    // /**
    //  * @return ConditionVisitPatient[] Returns an array of ConditionVisitPatient objects
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
    public function findOneBySomeField($value): ?ConditionVisitPatient
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
