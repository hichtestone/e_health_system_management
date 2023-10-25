<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationReviewAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationReviewAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationReviewAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationReviewAuditTrail[]    findAll()
 * @method DeviationReviewAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationReviewAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationReviewAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'r')
            ->innerJoin('r.deviation', 'd')
            ->innerJoin('d.project', 'p')
            ->leftJoin('at.user', 'u')
            ;
    }
}
