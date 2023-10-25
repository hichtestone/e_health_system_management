<?php

namespace App\ESM\Repository;

use App\ESM\Entity\PorteeDocumentTransverse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PorteeDocumentTransverse|null find($id, $lockMode = null, $lockVersion = null)
 * @method PorteeDocumentTransverse|null findOneBy(array $criteria, array $orderBy = null)
 * @method PorteeDocumentTransverse[]    findAll()
 * @method PorteeDocumentTransverse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PorteeDocumentTransverseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PorteeDocumentTransverse::class);
    }

    // /**
    //  * @return PorteeDocumentTransverse[] Returns an array of PorteeDocumentTransverse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PorteeDocumentTransverse
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
