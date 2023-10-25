<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationSampleCorrection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationSampleCorrection|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationSampleCorrection|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationSampleCorrection[]    findAll()
 * @method DeviationSampleCorrection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationSampleCorrectionRepository extends ServiceEntityRepository
{
	/**
	 * DeviationSampleCorrectionRepository constructor.
	 * @param ManagerRegistry $registry
	 */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationSampleCorrection::class);
    }
}
