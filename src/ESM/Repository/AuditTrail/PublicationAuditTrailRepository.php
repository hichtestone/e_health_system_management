<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PublicationAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublicationAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationAuditTrail[]    findAll()
 * @method PublicationAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationAuditTrail::class);
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
