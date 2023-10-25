<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DropdownList\TrailTreatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrailTreatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrailTreatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrailTreatment[]    findAll()
 * @method TrailTreatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrailTreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrailTreatment::class);
    }
}
