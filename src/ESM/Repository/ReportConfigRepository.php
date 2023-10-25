<?php

namespace App\ESM\Repository;

use App\ESM\Entity\ReportConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportConfig[]    findAll()
 * @method ReportConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportConfigRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ReportConfig::class);
	}
}
