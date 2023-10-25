<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexListGenProjectPublication($idProject)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.project', 'pr')
            ->innerJoin('p.communicationType', 'c')
            ->andWhere('pr.id = :idProject')
            ->setParameter('idProject', $idProject)
            ;
    }
}
