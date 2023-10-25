<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ETMF\Entity\AuditTrail\DocumentAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentAuditTrail[]    findAll()
 * @method DocumentAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.zone', 'zone')
            ->innerJoin('e.section', 'section')
            ->innerJoin('e.artefact', 'artefact')
            ->innerJoin('e.sponsor', 'sponsor')
            ->innerJoin('e.project', 'project')
            ->leftJoin('at.user', 'u')
            ;
    }
}
