<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Zone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zone[]    findAll()
 * @method Zone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('zone');
	}

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$zones = [];

		$result = $this->createQueryBuilder('zone')
			->select('zone.id', 'zone.name')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$zones[$value['id']] = $value['name'];}

		return $zones;
	}

    public function countName($name, $idCurrent)
    {
        $conn = $this->getEntityManager()->getConnection();

        if ($idCurrent) {
            $sql = 'SELECT count(id) as counter
            FROM zone
            WHERE name = :name 
            AND deleted_at IS NULL
            AND id != :idCurrent;
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['name' => $name, 'idCurrent' => $idCurrent]);
        } else {
            $sql = 'SELECT count(id) as counter
            FROM zone
            WHERE name = :name 
            AND deleted_at IS NULL
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['name' => $name]);
        }

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }
    public function countCode($code, $idCurrent)
    {
        $conn = $this->getEntityManager()->getConnection();

        if ('' != $idCurrent) {
            $sql = 'SELECT count(.id) as counter
            FROM zone
            WHERE code = :code 
            AND deleted_at = IS NULL
            AND id != :idCurrent;
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['code' => $code, 'idCurrent' => $idCurrent]);
        } else {
            $sql = 'SELECT count(id) as counter
            FROM zone
            WHERE code = :code 
            AND deleted_at IS NULL 
             ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['code' => $code]);
        }


        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

}
