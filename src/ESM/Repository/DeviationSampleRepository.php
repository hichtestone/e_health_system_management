<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSample|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSample|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSample[]    findAll()
 * @method DeviationSample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationSample::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('sample')
			->leftJoin('sample.projects', 'project')
			->leftJoin('sample.processInvolves', 'processInvolves');
	}

	/**
	 * @param int $draftMode
	 * @return int
	 */
	public function getLastDeviationSampleId(int $draftMode): int
	{
		$statusDeviation = $draftMode ? Deviation::STATUS_DRAFT : Deviation::STATUS_IN_PROGRESS;

		$qb = $this->createQueryBuilder('deviationSample')
			->select('deviationSample.id')
			->andWhere('deviationSample.status =:status')
			->setParameter('status', $statusDeviation)
			->orderBy('deviationSample.id', 'DESC')
			->setMaxResults(1);

		$res = $qb->getQuery()->getScalarResult();

		return count($res) > 0 ? (int) $res[0]['id'] : 0;
	}
}
