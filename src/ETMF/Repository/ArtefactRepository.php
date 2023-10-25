<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\Artefact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Artefact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artefact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artefact[]    findAll()
 * @method Artefact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtefactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artefact::class);
    }

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('artefact')
			->leftJoin('artefact.section', 's');
	}

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$artefacts = [];

		$result = $this->createQueryBuilder('artefact')
			->select('artefact.id', 'artefact.name')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$artefacts[$value['id']] = $value['name'];}

		return $artefacts;
	}
}
