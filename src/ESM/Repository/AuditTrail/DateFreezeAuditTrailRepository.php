<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ProjectDatabaseFreezeAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectDatabaseFreezeAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectDatabaseFreezeAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectDatabaseFreezeAuditTrail[]    findAll()
 * @method ProjectDatabaseFreezeAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateFreezeAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectDatabaseFreezeAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('e.projectDate', 'd')
            ->innerJoin('d.project', 'p')
            ->leftJoin('at.user', 'u')
            ;
    }
}
