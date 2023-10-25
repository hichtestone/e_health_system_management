<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ETMF\Entity\AuditTrail\DocumentAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use App\ETMF\Entity\AuditTrail\DocumentVersionAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentVersionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentVersionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentVersionAuditTrail[]    findAll()
 * @method DocumentVersionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentVersionAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentVersionAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.document', 'document')
            ->leftJoin('at.user', 'u')
            ;
    }
}
