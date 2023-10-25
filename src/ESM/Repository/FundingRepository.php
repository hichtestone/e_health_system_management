<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Funding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Funding|null find($id, $lockMode = null, $lockVersion = null)
 * @method Funding|null findOneBy(array $criteria, array $orderBy = null)
 * @method Funding[]    findAll()
 * @method Funding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Funding::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexListGenProjectFunding($id)
    {
        $qb = $this->createQueryBuilder('f')
            ->innerJoin('f.project', 'p')
            ->innerJoin('f.funder', 'fu')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb;
    }
}
