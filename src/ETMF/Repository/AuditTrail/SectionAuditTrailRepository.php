<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ESM\Repository\AuditTrailTrait;
use App\ETMF\Entity\AuditTrail\SectionAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionAuditTrail[]    findAll()
 * @method SectionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.zone', 'zone')
            ->leftJoin('at.user', 'u')
            ;
    }
}
