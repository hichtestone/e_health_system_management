<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Drug;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Drug|null find($id, $lockMode = null, $lockVersion = null)
 * @method Drug|null findOneBy(array $criteria, array $orderBy = null)
 * @method Drug[]    findAll()
 * @method Drug[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drug::class);
    }

    /**
     * @param bool $displayArchived
     * @return QueryBuilder
     */
    public function indexListGen(bool $displayArchived)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->from(Drug::class, 'i');

        return $displayArchived ? $query : $query->andWhere('i.deletedAt IS NULL');
    }

    /**
     * @return QueryBuilder
     */
    public function DrugToValidate()
    {
        $q = $this->createQueryBuilder('d')
            ->innerJoin('d.documentTransverses', 'doc')
            ->where('doc in(SELECT c FROM App\ESM\Entity\DocumentTransverse c WHERE c.isValid = false)')
            ->getQuery();

        return $q->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function GetDrugByProject(User $user, Drug $drug)
    {
        $q = $this->createQueryBuilder('dr')
            ->innerJoin('dr.projects ', 'pr')
            ->innerJoin('pr.userProjects', 'u')
            ->where('u.user = :user_id')
            ->andWhere('dr.id = :drug_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('drug_id', $drug->getId())
            ->getQuery();

        return $q->getResult();
    }

    public function findByTreatmentHasDoc(string $ids)
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.TrailTreatment', 't')
            ->leftJoin('d.documentTransverses', 'doc')
            ->where('doc.id IS NOT NULL')
            ->andWhere('t.id IN (:ids)')
            ->setParameter('ids', explode(',', $ids))
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function findAllHasDoc()
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.documentTransverses', 'doc')
            ->where('doc.id IS NOT NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->andWhere('d.isValid = true')
            ->getQuery()
            ->getResult();

        return $qb;
    }
}
