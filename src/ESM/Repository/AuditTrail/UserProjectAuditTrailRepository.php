<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\UserProjectAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserProjectAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProjectAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProjectAuditTrail[]    findAll()
 * @method UserProjectAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProjectAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProjectAuditTrail::class);
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
