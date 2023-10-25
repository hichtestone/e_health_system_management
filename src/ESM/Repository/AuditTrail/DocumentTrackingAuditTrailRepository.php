<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DocumentTrackingAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTrackingAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTrackingAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTrackingAuditTrail[]    findAll()
 * @method DocumentTrackingAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTrackingAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTrackingAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.project', 'p')
            ->leftJoin('at.user', 'u')
        ;
    }
}
