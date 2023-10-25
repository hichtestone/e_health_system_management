<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Center;
use App\ESM\Entity\Project;
use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportVisit[]    findAll()
 * @method ReportVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportVisit::class);
    }

	/**
	 * @param Project $project
	 * @param Center $center
	 * @return QueryBuilder
	 */
    public function indexListGen(Project $project, Center $center)
    {
        return $this->createQueryBuilder('reportVisit')
            ->leftJoin('reportVisit.project', 'project')
            ->leftJoin('reportVisit.center', 'center')
            ->where('project.id = :projectID')
            ->andWhere('center.id = :centerID')
            ->setParameter('projectID', $project->getId())
            ->setParameter('centerID', $center->getId());
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
	public function visitsCenterGen(Project $project)
	{
		return $this->createQueryBuilder('reportVisit')
			->leftJoin('reportVisit.project', 'project')
			->leftJoin('reportVisit.center', 'center')
			->leftJoin('reportVisit.reportConfigVersion', 'reportConfigVersion')
			->leftJoin('reportConfigVersion.config', 'config')
			->leftJoin('config.modelVersion', 'modelVersion')
			->where('project.id = :projectID')
			->andWhere('reportVisit.reportedAt IS NOT NULL')
			->setParameter('projectID', $project->getId());
	}

}
