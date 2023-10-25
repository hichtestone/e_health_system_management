<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ETMF\Entity\AuditTrail\ZoneAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ZoneAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZoneAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZoneAuditTrail[]    findAll()
 * @method ZoneAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZoneAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ;
    }
}
