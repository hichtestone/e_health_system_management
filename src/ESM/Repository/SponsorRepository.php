<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DropdownList\Sponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sponsor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sponsor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sponsor[]    findAll()
 * @method Sponsor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsorRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Sponsor::class);
	}

	public function getAllID(): array
	{
		$sponsors = [];

		$result = $this->createQueryBuilder('sponsor')
			->select('sponsor.id', 'sponsor.label')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$sponsors[$value['id']] = $value['label'];}

		return $sponsors;
	}
}
