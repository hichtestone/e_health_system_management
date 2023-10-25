<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\ReportConfigVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportConfigVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportConfigVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportConfigVersion[]    findAll()
 * @method ReportConfigVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportConfigVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportConfigVersion::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(Project $project)
	{
		return $this->createQueryBuilder('reportConfigVersion')
			->leftJoin('reportConfigVersion.config', 'config')
			->leftJoin('config.project', 'project')
			->leftJoin('config.modelVersion', 'modelVersion')
			->leftJoin('modelVersion.reportModel', 'model')
			->leftJoin('model.versions', 'versions')
			->where('config.project = :projectID')
			->setParameter('projectID', $project->getId())
		;
	}
}
