<?php

namespace App\ESM\Repository;

use App\ESM\Entity\VersionDocumentTransverse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VersionDocumentTransverse|null find($id, $lockMode = null, $lockVersion = null)
 * @method VersionDocumentTransverse|null findOneBy(array $criteria, array $orderBy = null)
 * @method VersionDocumentTransverse[]    findAll()
 * @method VersionDocumentTransverse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionDocumentTransverseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VersionDocumentTransverse::class);
    }

    public function findOneBySomeField($version, $doc)
    {
        return $this->createQueryBuilder('v')
            ->Where('v.version = :val')
            ->andWhere('v.documentTransverse= :doc')
            ->setParameter('val', $version)
            ->setParameter('doc', $doc)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByDocumentWithFile($doc)
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.filename1 IS NOT NULL')
            ->andWhere('v.documentTransverse= :document')
            ->setParameter('document', $doc)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findByTypeAndDoc(string $code)
    {
        $qb = $this->createQueryBuilder('v')
            ->innerJoin('v.documentTransverse', 'doc')
            ->innerJoin('doc.TypeDocument', 'tdoc')
            ->leftJoin('doc.drug', 'm')
            ->where('m.id IS NOT NULL')
            ->andWhere('doc.deletedAt IS NULL')
            ->andWhere('tdoc.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();

        return $qb;
    }
}
