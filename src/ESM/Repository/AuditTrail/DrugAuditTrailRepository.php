<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DrugAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DrugAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrugAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrugAuditTrail[]    findAll()
 * @method DrugAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrugAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrugAuditTrail::class);
    }
}
