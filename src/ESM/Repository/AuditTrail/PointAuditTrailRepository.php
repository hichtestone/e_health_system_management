<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PointAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PointAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PointAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PointAuditTrail[]    findAll()
 * @method PointAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointAuditTrail::class);
    }
}
