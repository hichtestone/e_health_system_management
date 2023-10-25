<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DeviationAndSample;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviationAndSample|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviationAndSample|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviationAndSample[]    findAll()
 * @method DeviationAndSample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviationAndSampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviationAndSample::class);
    }
}
