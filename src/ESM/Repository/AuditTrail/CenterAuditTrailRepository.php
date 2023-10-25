<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\CenterAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CenterAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CenterAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CenterAuditTrail[]    findAll()
 * @method CenterAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CenterAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.interlocutorCenters', 'ic')
            ->innerJoin('ic.interlocutor', 'i')
            ->innerJoin('e.project', 'p')
            ->leftJoin('at.user', 'u')
            ;
    }
}
