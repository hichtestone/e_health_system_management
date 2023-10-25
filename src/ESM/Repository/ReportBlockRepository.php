<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportBlock[]    findAll()
 * @method ReportBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportBlock::class);
    }

    public function countBlock($reportModelVersionId, $name, $idCurrent)
    {
        $conn = $this->getEntityManager()->getConnection();

        if ('' != $idCurrent) {
            $sql = 'SELECT
                COUNT(report_block.id) AS counter
            FROM
                report_model_version_block
            INNER JOIN report_model_version ON report_model_version_block.version_id = report_model_version.id
            INNER JOIN report_block ON report_model_version_block.block_id = report_block.id
            WHERE
                report_model_version.id = :reportModelVersionId 
            AND report_block.name = :name
            AND report_block.id != :idCurrent ';

            $stmt = $conn->prepare($sql);
            $stmt->execute(['reportModelVersionId' => $reportModelVersionId, 'name' => $name, 'idCurrent' => $idCurrent]);
        } else {
            $sql = 'SELECT
                COUNT(report_block.id) AS counter
            FROM
                report_model_version_block
            INNER JOIN report_model_version ON report_model_version_block.version_id = report_model_version.id
            INNER JOIN report_block ON report_model_version_block.block_id = report_block.id
            WHERE
                report_model_version.id = :reportModelVersionId 
            AND report_block.name = :name';

            $stmt = $conn->prepare($sql);
            $stmt->execute(['reportModelVersionId' => $reportModelVersionId, 'name' => $name]);
        }

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    /**
     * @param false $system
     *
     * @return int|mixed|string
     */
    public function getAllBlock(bool $system = false)
    {
        $qb = $this->createQueryBuilder('report_block')
            ->where('report_block.sys =:system')
            ->setParameter('system', $system);

        return $qb->getQuery()->getResult();
    }

	/**
	 * @param $typeReport
	 * @return int|mixed|string
	 */
	public function getBlockSystemByModel($typeReport)
	{
		if ($typeReport === ReportModel::REPORT_IN_FOLLOW_UP) {

			$blockSystemsName = [
				'identification',
				'participants',
				'validation',
				'follow_up',
				'table_of_patients_notes_checked',
				'patient_status',
				'action_issues_log',
				'deviations_log'
			];

		} elseif ($typeReport === ReportModel::REPORT_PROD) {

			$blockSystemsName = [
				'identification',
				'participants',
				'validation',
				'date_of_visits',
				'documents_discussed',
				'action_issues_log'
			];

		} elseif ($typeReport === ReportModel::REPORT_CLOS) {

			$blockSystemsName = [
				'identification',
				'participants',
				'validation',
				'date_of_visits',
				'close_out',
				'patient_status',
				'action_issues_log',
				'deviations_log'
			];
		}

		$qb = $this->createQueryBuilder('report_block')
			->andWhere('report_block.sys =:system')
			->andWhere('report_block.name IN (:blockSystemsName)')
			->setParameters([
				'system' 			=> true,
				'blockSystemsName'  => $blockSystemsName
			]);

		return $qb->getQuery()->getResult();
	}
}
