<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ReportConfigAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportConfigAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportConfigAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportConfigAuditTrail[]    findAll()
 * @method ReportConfigAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportConfigAuditTrailRepository extends ServiceEntityRepository
{
	use AuditTrailTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ReportConfigAuditTrail::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function auditTrailListGen()
	{
	}
}
