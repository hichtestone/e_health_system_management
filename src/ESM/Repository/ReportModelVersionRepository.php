<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportModelVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportModelVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportModelVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportModelVersion[]    findAll()
 * @method ReportModelVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportModelVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportModelVersion::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen()
    {
        return $this->createQueryBuilder('reportModelVersion')
            ->leftJoin('reportModelVersion.reportModel', 'reportModel')
            ->where('reportModel.deletedAt IS NULL');
    }
}
