<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationSystemCorrectionAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemCorrectionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemCorrectionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemCorrectionAuditTrail[]    findAll()
 * @method DeviationSystemCorrectionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemCorrectionAuditTrailRepository extends ServiceEntityRepository
{
	use AuditTrailTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemCorrectionAuditTrail::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function auditTrailListGen()
	{
		return $this->createQueryBuilder('at')
			->innerJoin('at.entity', 'd')
			->leftJoin('at.user', 'u')
			;
	}
}
