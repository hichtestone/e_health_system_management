<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSystemReviewAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemReviewAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemReviewAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemReviewAction[]    findAll()
 * @method DeviationSystemReviewAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemReviewActionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemReviewAction::class);
	}
}
