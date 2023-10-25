<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationActionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationActionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationActionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationActionAuditTrail[]    findAll()
 * @method DeviationActionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationActionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationActionAuditTrail::class);
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
            ;
    }
}
