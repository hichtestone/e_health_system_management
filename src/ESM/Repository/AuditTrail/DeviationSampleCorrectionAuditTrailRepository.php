<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationSampleCorrectionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSampleCorrectionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSampleCorrectionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSampleCorrectionAuditTrail[]    findAll()
 * @method DeviationSampleCorrectionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSampleCorrectionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationSampleCorrectionAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'd')
            ->leftJoin('at.user', 'u')
            ;
    }
}
