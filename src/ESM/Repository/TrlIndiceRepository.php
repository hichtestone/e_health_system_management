<?php

namespace App\ESM\Repository;

use App\ESM\Entity\TrlIndice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrlIndice|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrlIndice|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrlIndice[]    findAll()
 * @method TrlIndice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrlIndiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrlIndice::class);
    }

    // /**
    //  * @return TrlIndice[] Returns an array of TrlIndice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrlIndice
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
