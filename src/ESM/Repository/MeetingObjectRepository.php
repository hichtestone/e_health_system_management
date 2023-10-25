<?php

namespace App\ESM\Repository;

use App\ESM\Entity\MeetingObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingObject[]    findAll()
 * @method MeetingObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingObject::class);
    }

    // /**
    //  * @return MeetingObject[] Returns an array of MeetingObject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeetingObject
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
