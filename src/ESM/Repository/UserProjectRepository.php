<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Entity\UserProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProject[]    findAll()
 * @method UserProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProject::class);
    }

    public function homeListGen(User $user, bool $includeClosedProject): QueryBuilder
	{
        $qb = $this->createQueryBuilder('up')
            ->innerJoin('up.project', 'p')
            ->innerJoin('p.sponsor', 's')
            ->innerJoin('p.responsiblePM', 'r')
            ->where('up.user = :user')
            ->andWhere('up.disabledAt IS NULL')
            ->setParameter('user', $user)
        ;
        if (!$includeClosedProject) {
            $qb->andWhere('p.closedAt IS NULL');
        }

        return $qb;
    }

    public function indexListGen(Project $project): QueryBuilder
	{
		return $this->createQueryBuilder('up')
			->innerJoin('up.project', 'p')
			->innerJoin('up.user', 'u')
			->leftJoin('u.civility', 'c')
			->where('up.project = :project')
			->setParameter('project', $project);
    }
}
