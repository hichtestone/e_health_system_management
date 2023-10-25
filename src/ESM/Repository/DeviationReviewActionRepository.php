<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationReviewAction;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationReviewAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationReviewAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationReviewAction[]    findAll()
 * @method DeviationReviewAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationReviewActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationReviewAction::class);
    }
}
