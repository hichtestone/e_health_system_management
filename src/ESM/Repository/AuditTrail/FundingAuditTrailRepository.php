<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\FundingAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FundingAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundingAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundingAuditTrail[]    findAll()
 * @method FundingAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundingAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundingAuditTrail::class);
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
