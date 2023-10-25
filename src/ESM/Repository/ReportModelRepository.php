<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportModel[]    findAll()
 * @method ReportModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportModel::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen()
    {
        return $this->createQueryBuilder('reportModel')
                    ->leftJoin('reportModel.versions', 'versions')
                    ->where('reportModel.deletedAt IS NULL');
    }

    public function countModel($name, $idCurrent)
    {
        $conn = $this->getEntityManager()->getConnection();

        if ('' != $idCurrent) {
            $sql = 'SELECT count(report_model.id) as counter
            FROM `report_model`
            WHERE report_model.name = :name 
            AND report_model.deleted_at IS NULL 
            AND report_model.id != :idCurrent;
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['name' => $name, 'idCurrent' => $idCurrent]);
        } else {
            $sql = 'SELECT count(report_model.id) as counter
            FROM `report_model`
            WHERE report_model.name = :name 
            AND report_model.deleted_at IS NULL;
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['name' => $name]);
        }

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    /**
     * @return QueryBuilder
     */
    public function indexSettingListGen()
    {
        return $this->createQueryBuilder('reportModel')
                    ->leftJoin('reportModel.versions', 'versions')
                    ->where('versions.publishedAt IS NOT NULL OR versions.obsoletedAt IS NOT NULL');
    }
}
