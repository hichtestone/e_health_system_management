<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportConfigVersionBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportConfigVersionBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportConfigVersionBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportConfigVersionBlock[]    findAll()
 * @method ReportConfigVersionBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportConfigVersionBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportConfigVersionBlock::class);
    }
}
