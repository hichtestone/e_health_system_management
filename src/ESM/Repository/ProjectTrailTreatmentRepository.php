<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ProjectTrailTreatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectTrailTreatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTrailTreatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTrailTreatment[]    findAll()
 * @method ProjectTrailTreatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTrailTreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTrailTreatment::class);
    }
}
