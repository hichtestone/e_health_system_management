<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\DeviationSystemActionAuditTrail;
use App\ESM\Entity\AuditTrail\DeviationSystemReviewAuditTrail;
use App\ESM\Repository\AuditTrailTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemReviewAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemReviewAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemReviewAuditTrail[]    findAll()
 * @method DeviationSystemReviewAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemReviewAuditTrailRepository extends ServiceEntityRepository
{
	use AuditTrailTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemReviewAuditTrail::class);
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
