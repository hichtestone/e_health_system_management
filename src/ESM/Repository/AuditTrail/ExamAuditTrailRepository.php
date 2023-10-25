<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ExamAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamAuditTrail[]    findAll()
 * @method ExamAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamAuditTrail::class);
    }
}
