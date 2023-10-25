<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\SubmissionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubmissionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubmissionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubmissionAuditTrail[]    findAll()
 * @method SubmissionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubmissionAuditTrail::class);
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
