<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportModelVersionBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportModelVersionBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportModelVersionBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportModelVersionBlock[]    findAll()
 * @method ReportModelVersionBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportModelVersionBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportModelVersionBlock::class);
    }
}
