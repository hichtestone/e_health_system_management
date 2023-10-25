<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportBlockParam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportBlockParam|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportBlockParam|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportBlockParam[]    findAll()
 * @method ReportBlockParam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportBlockParamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportBlockParam::class);
    }
}
