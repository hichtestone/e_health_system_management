<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DocumentTrackingCenterAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTrackingCenterAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTrackingCenterAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTrackingCenterAuditTrail[]    findAll()
 * @method DocumentTrackingCenterAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTrackingCenterAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTrackingCenterAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.center', 'c')
            ->innerJoin('c.project', 'p')
            ->leftJoin('at.user', 'u')
        ;
    }
}
