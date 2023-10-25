<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchemaCondition|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchemaCondition|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchemaCondition[]    findAll()
 * @method SchemaCondition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchemaConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchemaCondition::class);
    }

    public function findByProject(Project $project)
    {
        return $this->createQueryBuilder('sc')
            ->addSelect('v')
            ->addSelect('p')
            ->innerJoin('sc.visits', 'v')
            ->innerJoin('sc.phases', 'p')
            ->where('p.project = :project')
            ->setParameter('project', $project)
            ->orderBy('p.label')
            ->andWhere('sc.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(Project $project)
    {
        return $this->createQueryBuilder('sc')
            ->leftJoin('sc.visit', 'v')
            ->leftJoin('sc.phase', 'p')
            ->where('sc.project = :project')
            ->setParameter('project', $project);
    }
}
