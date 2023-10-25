<?php

namespace App\ESM\Repository\AuditTrail;

use App\ESM\Entity\AuditTrail\PatientDataAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientDataAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientDataAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientDataAuditTrail[]    findAll()
 * @method PatientDataAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientDataAuditTrailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientDataAuditTrail::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function auditTrailListGen()
	{
		return $this->createQueryBuilder('at')
			->leftJoin('at.entity', 'e')
			->leftJoin('e.patient', 'p')
			->leftJoin('e.variable', 'v')
			->leftJoin('p.project', 'project')
			->leftJoin('p.center', 'c')
			->leftJoin('at.user', 'u')
			;
	}
}
