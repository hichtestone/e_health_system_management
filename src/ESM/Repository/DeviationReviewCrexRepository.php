<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationReview[]    findAll()
 * @method DeviationReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationReviewCrexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationReview::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from(DeviationReview::class, 'dr')
            ->innerJoin('dr.deviation', 'd')
            ->innerJoin('d.project', 'p')
            ->where('dr.isCrex = 1')
            ->andWhere('dr.deletedAt IS NULL');
    }
}
