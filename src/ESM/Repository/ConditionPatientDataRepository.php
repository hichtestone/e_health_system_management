<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ConditionPatientData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConditionPatientData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConditionPatientData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConditionPatientData[]    findAll()
 * @method ConditionPatientData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionPatientDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConditionPatientData::class);
    }

    // /**
    //  * @return ConditionPatientData[] Returns an array of ConditionPatientData objects
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
    public function findOneBySomeField($value): ?ConditionPatientData
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
