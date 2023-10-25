<?php

namespace App\ESM\Repository;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhaseSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhaseSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhaseSetting[]    findAll()
 * @method PhaseSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhaseSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhaseSetting::class);
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
    public function indexListGen(Project $project): QueryBuilder
	{
        return $this->createQueryBuilder('p')
			->leftJoin('p.phaseSettingStatus', 'phaseSettingStatus')
            ->orderBy('p.position', 'ASC')
            ->where('p.project = :project')
            ->setParameter('project', $project);
    }
}
