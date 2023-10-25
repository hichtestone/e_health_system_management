<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemReview[]    findAll()
 * @method DeviationSystemReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemReviewRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemReview::class);
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return QueryBuilder
	 */
	public function indexListGen(DeviationSystem $deviationSystem): QueryBuilder
	{
		return $this->createQueryBuilder('deviation_system_review')
			->innerJoin('deviation_system_review.deviationSystem', 'deviation_system')
			->leftJoin('deviation_system_review.reader', 'reader')
			->andWhere('deviation_system.id = :deviationSystemID')
			->andWhere('deviation_system_review.isCrex = 0')
			->andWhere('deviation_system_review.deletedAt IS NULL')
			->setParameter('deviationSystemID', $deviationSystem->getId());
	}

	/**
	 * @param DeviationSystem $deviationSystem
	 * @return QueryBuilder
	 */
	public function indexListGenCrex(DeviationSystem $deviationSystem): QueryBuilder
	{
		return $this->createQueryBuilder('deviation_system_review')
			->innerJoin('deviation_system_review.deviationSystem', 'deviation_system')
			->leftJoin('deviation_system_review.reader', 'reader')
			->andWhere('deviation_system.id = :deviationSystemID')
			->andWhere('deviation_system_review.isCrex = 1')
			->andWhere('deviation_system_review.deletedAt IS NULL')
			->setParameter('deviationSystemID', $deviationSystem->getId());
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGenCrexNCSystem(): QueryBuilder
	{
		return $this->getEntityManager()->createQueryBuilder()
			->from(DeviationSystemReview::class, 'deviation_system_review')
			->innerJoin('deviation_system_review.deviationSystem', 'deviation_system')
			->where('deviation_system_review.isCrex = 1')
			->andWhere('deviation_system_review.deletedAt IS NULL');
	}
}
