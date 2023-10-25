<?php

namespace App\ESM\Repository;

use App\ESM\Entity\MembershipGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipGroup[]    findAll()
 * @method MembershipGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipGroup::class);
    }

    // /**
    //  * @return MembershipGroup[] Returns an array of MembershipGroup objects
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
    public function findOneBySomeField($value): ?MembershipGroup
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
