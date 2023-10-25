<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemCorrection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemCorrection|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemCorrection|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemCorrection[]    findAll()
 * @method DeviationSystemCorrection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemCorrectionRepository extends ServiceEntityRepository
{
	/**
	 * DeviationSystemCorrectionRepository constructor.
	 * @param ManagerRegistry $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemCorrection::class);
	}

	/*/**
	 * @param DeviationSystem $deviationSystem
	 * @return QueryBuilder
	 */
	/*public function indexListGen(DeviationSystem $deviationSystem): QueryBuilder
	{
		return $this->createQueryBuilder('deviation_system_correction')
			->leftJoin('deviation_system_correction.deviationSystem', 'deviation_system')
			->andWhere('deviation_system.id = :deviationSystemID')
			->andWhere('deviation_system_correction.deletedAt is null')
			->setParameter('deviationID', $deviationSystem->getId());
	}*/
}
