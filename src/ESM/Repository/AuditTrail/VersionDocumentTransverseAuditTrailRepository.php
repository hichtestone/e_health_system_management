<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\VersionDocumentTransverseAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VersionDocumentTransverseAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method VersionDocumentTransverseAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method VersionDocumentTransverseAuditTrail[]    findAll()
 * @method VersionDocumentTransverseAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionDocumentTransverseAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VersionDocumentTransverseAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ->innerJoin('e.documentTransverse', 'd');
    }
}
