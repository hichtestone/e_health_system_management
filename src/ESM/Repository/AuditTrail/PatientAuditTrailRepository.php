<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PatientAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAuditTrail[]    findAll()
 * @method PatientAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ->leftJoin('e.center', 'c')
            ->leftJoin('e.project', 'p')
            ;
    }

}
