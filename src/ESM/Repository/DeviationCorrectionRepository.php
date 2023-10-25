<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationCorrection|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationCorrection|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationCorrection[]    findAll()
 * @method DeviationCorrection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationCorrectionRepository extends ServiceEntityRepository
{
    /**
     * DeviationCorrectionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationCorrection::class);
    }

    /**
     * @param Project $project
     * @param Deviation $deviation
     * @return QueryBuilder
     */
    public function indexListGen(Project $project, Deviation $deviation): QueryBuilder
    {
        return $this->createQueryBuilder('dc')
            ->leftJoin('dc.project', 'p')
            ->leftJoin('dc.deviation', 'd')
            ->andWhere('p.id = :projectID')
            ->andWhere('d.id = :deviationID')
            ->andWhere('dc.deletedAt is null')
            ->setParameter('projectID', $project->getId())
            ->setParameter('deviationID', $deviation->getId());

    }
}
