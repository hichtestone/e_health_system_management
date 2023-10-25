<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSystemAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSystemAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSystemAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSystemAction[]    findAll()
 * @method DeviationSystemAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSystemActionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DeviationSystemAction::class);
	}
}
