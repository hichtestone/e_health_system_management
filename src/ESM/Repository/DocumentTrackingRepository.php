<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTracking[]    findAll()
 * @method DocumentTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTracking::class);
    }

    public function indexListGen(Project $project)
    {
        return $this->createQueryBuilder('dt')
            ->leftJoin('dt.country', 'c')
            ->where('dt.project = :project')
            ->setParameter('project', $project);
    }
}
