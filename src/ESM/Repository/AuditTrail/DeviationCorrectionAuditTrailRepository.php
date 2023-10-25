<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationCorrectionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationCorrectionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationCorrectionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationCorrectionAuditTrail[]    findAll()
 * @method DeviationCorrectionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationCorrectionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationCorrectionAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.project', 'p')
            ->innerJoin('e.deviation', 'd')
            ->leftJoin('at.user', 'u')
            ;
    }
}
