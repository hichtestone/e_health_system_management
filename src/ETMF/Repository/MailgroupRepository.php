<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\Mailgroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mailgroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mailgroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mailgroup[]    findAll()
 * @method Mailgroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailgroupRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Mailgroup::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('mailgroup')
			->where('mailgroup.deletedAt IS NULL');
	}
}
