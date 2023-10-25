<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationReview[]    findAll()
 * @method DeviationReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationReview::class);
    }

    /**
     * @param Project $project
     * @param Deviation $deviation
     * @return QueryBuilder
     */
    public function indexListGen(Project $project, Deviation $deviation)
    {
        return $this->createQueryBuilder('dr')
            ->innerJoin('dr.deviation', 'd')
            ->innerJoin('d.project', 'p')
            ->leftJoin('dr.reader', 'r')
            ->where('p.id = :project')
            ->andWhere('d.id = :deviation')
            ->andWhere('dr.isCrex = 0')
            ->andWhere('dr.deletedAt IS NULL')
            ->setParameter('project', $project->getId())
            ->setParameter('deviation', $deviation->getId())
        ;
    }

    /**
     * @param Project $project
     * @param Deviation $deviation
     * @return QueryBuilder
     */
    public function indexListGenCrex(Project $project, Deviation $deviation)
    {
        return $this->createQueryBuilder('dr')
            ->innerJoin('dr.deviation', 'd')
            ->innerJoin('d.project', 'p')
            ->leftJoin('dr.reader', 'r')
            ->where('p.id = :project')
            ->andWhere('d.id = :deviation')
            ->andWhere('dr.isCrex = 1')
            ->andWhere('dr.deletedAt IS NULL')
            ->setParameter('project', $project->getId())
            ->setParameter('deviation', $deviation->getId())
        ;
    }
}
