<?php

namespace App\ESM\Repository;

use App\ESM\Entity\CourbeSetting;
use App\ESM\Entity\Point;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CourbeSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourbeSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourbeSetting[]    findAll()
 * @method CourbeSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourbeSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourbeSetting::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexListGen(Project $project)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(CourbeSetting::class, 'p')
            ->where('p.project = :project')
            ->setParameter('project', $project);

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function courbePoints(CourbeSetting $courbe)
    {
        $courbe = $this->getEntityManager()->find(1);
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(Point::class, p)
            ->where('p.courbesetting=:courbe')
            ->setParameter('courbesetting', $courbe);
    }
}
