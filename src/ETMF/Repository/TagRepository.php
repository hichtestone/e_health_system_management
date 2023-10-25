<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Tag::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('tag');
	}

    /**
     * @return array
     */
    public function getAllID(): array
    {
        $tags = [];

        $result = $this->createQueryBuilder('tag')
            ->select('tag.id', 'tag.name')
            ->getQuery()->getArrayResult();

        foreach ($result as $key => $value) {$tags[$value['id']] = $value['name'];}

        return $tags;
    }
}
