<?php

namespace App\ESM\Repository;

use App\ESM\Entity\PhaseSettingStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhaseSettingStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhaseSettingStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhaseSettingStatus[]    findAll()
 * @method PhaseSettingStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhaseSettingStatusRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, PhaseSettingStatus::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('phase_setting_status');
	}
}
