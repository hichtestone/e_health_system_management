<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ReportVisitAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportVisitAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportVisitAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportVisitAuditTrail[]    findAll()
 * @method ReportVisitAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportVisitAuditTrailRepository extends ServiceEntityRepository
{
	use AuditTrailTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ReportVisitAuditTrail::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function auditTrailListGen()
	{
		return $this->createQueryBuilder('at')
			->innerJoin('at.entity', 'reportVisit')
			->innerJoin('reportVisit.project', 'project')
			->innerJoin('reportVisit.center', 'center')
			->leftJoin('at.user', 'u');
	}
}
