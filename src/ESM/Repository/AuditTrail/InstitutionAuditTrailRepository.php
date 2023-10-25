<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\InstitutionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InstitutionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstitutionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstitutionAuditTrail[]    findAll()
 * @method InstitutionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstitutionAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.institutionType', 't')
            ->leftJoin('at.user', 'u')
        ;
    }
}
