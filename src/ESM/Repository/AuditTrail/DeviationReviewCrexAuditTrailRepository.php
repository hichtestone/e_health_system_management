<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationReviewAuditTrail;
use App\ESM\Entity\AuditTrail\DeviationReviewCrexAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationReviewCrexAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationReviewCrexAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationReviewCrexAuditTrail[]    findAll()
 * @method DeviationReviewCrexAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationReviewCrexAuditTrailRepository extends ServiceEntityRepository
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
                 ->innerJoin('at.entity', 'e')
                 ->innerJoin('e.deviation', 'd')
                 ->innerJoin('d.project', 'p')
                 ->leftJoin('at.user', 'u')
                 ->where('e.isCrex = 1')
                 ;
    }
}
