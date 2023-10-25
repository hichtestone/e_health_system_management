<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    /**
     * @param $id
     * @return QueryBuilder
     */
    public function indexListGenProjectSubmission($id): QueryBuilder
    {
		return $this->createQueryBuilder('s')
			->leftJoin('s.nameSubmissionRegulatory', 'n')
			->leftJoin('s.typeSubmissionRegulatory', 't')
			->leftJoin('s.typeSubmission', 'ts')
			->leftJoin('s.country', 'c')
			->leftJoin('s.project', 'p')
			->where('p.id = :id')
			->setParameter('id', $id)
        ;
    }

    public function findByProjectNotInCountries(Project $project, array $countryIds)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.nameSubmissionRegulatory', 'n')
            ->leftJoin('s.typeSubmissionRegulatory', 't')
            ->leftJoin('s.typeSubmission', 'ts')
            ->leftJoin('s.country', 'c')
            ->where('s.project = :project')
			->andWhere('c.id NOT IN (:ids)')
            ->setParameter('project', $project)
            ->setParameter('ids', $countryIds)
            ->getQuery()
            ->getResult();
    }
}
