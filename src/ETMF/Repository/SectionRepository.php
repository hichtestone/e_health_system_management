<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('section')
			->leftJoin('section.zone', 'zone');
	}

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$sections = [];

		$result = $this->createQueryBuilder('section')
			->select('section.id', 'section.name')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$sections[$value['id']] = $value['name'];}

		return $sections;
	}
}
