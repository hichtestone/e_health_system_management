<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DropdownList\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Country::class);
	}

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$countries = [];

		$result = $this->createQueryBuilder('Country')
			->select('Country.id', 'Country.name')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$countries[$value['id']] = $value['name'];}

		return $countries;
	}
}
