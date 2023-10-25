<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystem[]    findAll()
 * @method DeviationSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystem::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('system')
			->leftJoin('system.process', 'process');
	}

	/**
	 * @param int $draftMode
	 * @return int
	 */
	public function getLastDeviationSystemId(int $draftMode): int
	{
		$qb = $this->createQueryBuilder('deviationSystem')
			->select('deviationSystem.id');

		if ($draftMode) {
			$qb->andWhere('deviationSystem.status =:status')->setParameter('status', Deviation::STATUS_DRAFT);
		} else {
			$qb->andWhere('deviationSystem.status not in (:status)')->setParameter('status', Deviation::STATUS_DRAFT);
		}

		$qb->orderBy('deviationSystem.id', 'DESC')->setMaxResults(1);

		$res = $qb->getQuery()->getScalarResult();

		return count($res) > 0 ? (int) $res[0]['id'] : 0;
	}
}
