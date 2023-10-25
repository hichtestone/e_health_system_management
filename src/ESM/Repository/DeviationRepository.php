<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deviation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deviation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deviation[]    findAll()
 * @method Deviation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationRepository extends ServiceEntityRepository
{
    /**
     * DeviationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deviation::class);
    }

    /**
     * @param Project $project
     * @return QueryBuilder
     */
    public function indexListGen(Project $project): QueryBuilder
    {
        return $this->createQueryBuilder('deviation')
            ->leftJoin('deviation.project', 'project')
            ->leftJoin('deviation.center', 'center')
            ->leftJoin('deviation.reviews', 'reviews')
            ->where('project.id = :project')
            ->setParameter('project', $project->getId())
        ;
    }

    /**
     * @return int|mixed|string
     */
    public function getAllDeviationDraft()
    {
        $qb = $this->createQueryBuilder('deviation')
            ->andWhere('deviation.status =:status')
            ->setParameter('status', Deviation::STATUS_DRAFT);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $draftMode
     * @return int
     */
    public function getLastDeviationId(int $draftMode): int
    {
        $statusDeviation = $draftMode ? Deviation::STATUS_DRAFT : Deviation::STATUS_IN_PROGRESS;

        $qb = $this->createQueryBuilder('deviation')
            ->select('deviation.id')
            ->andWhere('deviation.status =:status')
            ->setParameter('status', $statusDeviation)
            ->orderBy('deviation.id', 'DESC')
            ->setMaxResults(1);

        $res = $qb->getQuery()->getScalarResult();

        return count($res) > 0 ? (int) $res[0]['id'] : 0;
    }
}
