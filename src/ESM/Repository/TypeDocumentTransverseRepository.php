<?php

namespace App\ESM\Repository;

use App\ESM\Entity\TypeDocumentTransverse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeDocumentTransverse|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDocumentTransverse|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDocumentTransverse[]    findAll()
 * @method TypeDocumentTransverse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDocumentTransverseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDocumentTransverse::class);
    }

    // /**
    //  * @return TypeDocumentTransverse[] Returns an array of TypeDocumentTransverse objects
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
    public function findOneBySomeField($value): ?TypeDocumentTransverse
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
