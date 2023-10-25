<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterlocutorCenter|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterlocutorCenter|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterlocutorCenter[]    findAll()
 * @method InterlocutorCenter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterlocutorCenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterlocutorCenter::class);
    }

    public function findByInterlocutorProject(Interlocutor $interlocutor, Project $project)
    {
        return $this->createQueryBuilder('ic')
            ->innerJoin('ic.service', 's')
            ->innerJoin('ic.center', 'c')
            ->innerJoin('s.institution', 'i')
            ->where('ic.interlocutor = :interlocutor')
            ->setParameter('interlocutor', $interlocutor)
            ->andWhere('c.project = :project')
            ->setParameter('project', $project)
            ->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.number')
            ->addOrderBy('i.name')
            ->addOrderBy('s.name')
            ->getQuery()
            ->getResult();
    }

    public function findByNotInInstitutions(Interlocutor $interlocutor, array $institutionIds)
    {
        return $this->createQueryBuilder('ic')
            ->innerJoin('ic.center', 'c')
            ->innerJoin('ic.service', 's')
            ->innerJoin('s.institution', 'i')
            ->where('ic.interlocutor = :interlocutor')
            ->setParameter('interlocutor', $interlocutor)
            ->andWhere('i.id NOT IN (:ids)')
            ->setParameter('ids', $institutionIds)
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('ic.disabledAt IS NULL')
            ->getQuery()
            ->getResult();
    }
}
