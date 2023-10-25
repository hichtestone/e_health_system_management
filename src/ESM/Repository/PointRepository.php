<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Point;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Point|null find($id, $lockMode = null, $lockVersion = null)
 * @method Point|null findOneBy(array $criteria, array $orderBy = null)
 * @method Point[]    findAll()
 * @method Point[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Point::class);
    }

    /**
     * @param $courbeSetting
     *
     * @return QueryBuilder
     */
    public function getPointsByCourbe($courbeSetting)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Point::class, 'p')
            ->andWhere('p.courbeSetting = :courbeSetting')
            ->setParameter('courbeSetting', $courbeSetting)->getQuery()->getResult();
    }
}
