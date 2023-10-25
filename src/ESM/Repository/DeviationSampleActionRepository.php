<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSampleAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSampleAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSampleAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSampleAction[]    findAll()
 * @method DeviationSampleAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSampleActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationSampleAction::class);
    }
}
