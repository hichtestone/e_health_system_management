<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\VisitPatientAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitPatientAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitPatientAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitPatientAuditTrail[]    findAll()
 * @method VisitPatientAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitPatientAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitPatientAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ->leftJoin('e.visit', 'v')
            ->leftJoin('e.patient', 'p')
            ;
    }
}
