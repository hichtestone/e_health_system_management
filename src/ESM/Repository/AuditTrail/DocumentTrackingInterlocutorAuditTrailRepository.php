<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DocumentTrackingInterlocutorAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTrackingInterlocutorAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTrackingInterlocutorAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTrackingInterlocutorAuditTrail[]    findAll()
 * @method DocumentTrackingInterlocutorAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTrackingInterlocutorAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTrackingInterlocutorAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.interlocutor', 'i')
            ->innerJoin('e.documentTracking', 'd')
            ->innerJoin('d.project', 'p')
            ->leftJoin('at.user', 'u')
        ;
    }
}
