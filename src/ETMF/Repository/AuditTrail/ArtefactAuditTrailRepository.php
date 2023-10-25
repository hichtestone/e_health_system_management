<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ESM\Repository\AuditTrailTrait;
use App\ETMF\Entity\AuditTrail\ArtefactAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArtefactAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArtefactAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArtefactAuditTrail[]    findAll()
 * @method ArtefactAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtefactAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtefactAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.section', 'section')
            ->leftJoin('at.user', 'u')
            ;
    }
}
