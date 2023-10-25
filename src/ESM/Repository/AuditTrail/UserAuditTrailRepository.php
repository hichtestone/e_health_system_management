<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\UserAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAuditTrail[]    findAll()
 * @method UserAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAuditTrail::class);
    }

    public function indexListGenAudit($type)
    {
        return  $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(UserAuditTrail::class, 'u')
            ->andWhere('u.modif_type = :type')
            ->setParameter('type', $type);
    }
}
