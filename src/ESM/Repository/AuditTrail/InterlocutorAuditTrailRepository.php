<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\InterlocutorAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterlocutorAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterlocutorAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterlocutorAuditTrail[]    findAll()
 * @method InterlocutorAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterlocutorAuditTrailRepository extends ServiceEntityRepository
{
    use AuditTrailTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterlocutorAuditTrail::class);
    }

    /* public function indexListGenAuditParticipant()
     {
         return $qb = $this->getEntityManager()->createQueryBuilder()
             ->select('*')
             ->from(InterlocutorAuditTrail::class, 'p')
             ->andWhere('p.modif_type = 1');
     }*/

    /**
     * @return QueryBuilder
     */
    public function auditTrailListGen()
    {
        return $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->innerJoin('e.job', 'j')
            ->leftJoin('at.user', 'u')
            ;
    }
}
