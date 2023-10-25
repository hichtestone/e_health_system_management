<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ProfileAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProfileAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileAuditTrail[]    findAll()
 * @method ProfileAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileAuditTrail::class);
    }
}
