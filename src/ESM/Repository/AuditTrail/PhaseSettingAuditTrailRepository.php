<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PhaseSettingAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhaseSettingAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhaseSettingAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhaseSettingAuditTrail[]    findAll()
 * @method PhaseSettingAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhaseSettingAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhaseSettingAuditTrail::class);
    }
}
