<?php

namespace App\ETMF\Repository\AuditTrail;

use App\ETMF\Entity\AuditTrail\MailgroupAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MailgroupAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailgroupAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailgroupAuditTrail[]    findAll()
 * @method MailgroupAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailgroupAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailgroupAuditTrail::class);
    }

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ;
    }
}
