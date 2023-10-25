<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Institution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institution[]    findAll()
 * @method Institution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institution::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(bool $displayArchived)
    {
        $query = $this->createQueryBuilder('i')
                ->leftJoin('i.institutionType', 't');

        return $displayArchived ? $query : $query->andWhere('i.deletedAt IS NULL');
    }

	public function countInstitution($idCurrent, $finess)
	{
		$conn = $this->getEntityManager()->getConnection();

		if ('' != $idCurrent) {
			$sql = 'SELECT
                COUNT(institution.id) AS counter
            FROM
                institution
            WHERE institution.finess = :finess
            AND institution.id != :idCurrent ';

			$stmt = $conn->prepare($sql);
			$stmt->execute(['idCurrent' => $idCurrent, 'finess' => $finess]);
		} else {
			$sql = 'SELECT
                COUNT(institution.id) AS counter
            FROM
                institution
            WHERE institution.finess = :finess';

			$stmt = $conn->prepare($sql);
			$stmt->execute(['finess' => $finess]);
		}

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchOne();
	}

	public function getInstitution($id)
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT
                country_id
            FROM
                institution
            WHERE institution.id = :id';

		$stmt = $conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchOne();
	}
}
