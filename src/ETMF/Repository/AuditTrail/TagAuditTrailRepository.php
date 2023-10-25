<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ETMF\Entity\AuditTrail\TagAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagAuditTrail[]    findAll()
 * @method TagAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagAuditTrail::class);
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
