<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\ReportModelAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportModelAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportModelAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportModelAuditTrail[]    findAll()
 * @method ReportModelAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportModelAuditTrailRepository extends ServiceEntityRepository
{
	use AuditTrailTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ReportModelAuditTrail::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function auditTrailListGen()
	{
	}
}
