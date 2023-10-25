<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\TrainingAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingAuditTrail[]    findAll()
 * @method TrainingAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingAuditTrail::class);
    }

    // /**
    //  * @return TrainingAuditTrail[] Returns an array of TrainingAuditTrail objects
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
    public function findOneBySomeField($value): ?TrainingAuditTrail
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
