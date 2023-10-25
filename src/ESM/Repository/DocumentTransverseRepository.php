<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTransverse|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTransverse|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTransverse[]    findAll()
 * @method DocumentTransverse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTransverseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTransverse::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexListGen()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from(DocumentTransverse::class, 'i')
        ;

        return $qb;
    }

    public function getDocumentByEntity($name, $drug, $institution, $interlocutor)
    {
        return $this->createQueryBuilder('d')
            ->where('d.name = :name')
            ->andWhere('d.interlocutor = :interlocutor OR d.drug  = :drug OR d.institution = :institution')
            ->setParameter('name', $name)
            ->setParameter('drug', $drug)
            ->setParameter('interlocutor', $interlocutor)
            ->setParameter('institution', $institution)
            ->getQuery()
            ->getResult();
    }

    /**
     * find by Query.
     */
    public function findAllQuery(User $user, array $query, $roles, string $context = '')
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.drug', 'drug')
            ->leftJoin('a.interlocutor', 'interlocutor')
            ->leftJoin('a.institution', 'institution')
            ->where('a.deletedAt IS NULL')
            ->andWhere('drug.deletedAt IS NULL')
            ->andWhere('interlocutor.deletedAt IS NULL')
            ->andWhere('institution.deletedAt IS NULL')
        ;

        // Medicaments
        if ('drug' === $context) {
            $qb->leftJoin('a.drug', 'dr')
                ->leftJoin('dr.projects ', 'pr')
                ->leftJoin('pr.userProjects', 'u')
                ->andWhere('u.user = :user_id')
                ->andWhere('u.disabledAt IS NULL')
                ->andWhere('dr.deletedAt IS NULL')
                ->setParameter('user_id', $user->getId());
        }

        // Institutions
        if ('institution' === $context) {
            $qb->innerJoin('a.institution', 'i')
                ->innerJoin('i.centers', 'cn')
                ->innerJoin('cn.project', 'cnpr')
                ->innerJoin('cnpr.userProjects', 'u')
                ->andWhere('u.user = :user_id')
                ->andWhere('u.disabledAt IS NULL')
                ->andWhere('i.deletedAt IS NULL')
                ->setParameter('user_id', $user->getId());
        }

        // Interlocuteur
        if ('interlocutor' === $context) {
            $qb->innerJoin('a.interlocutor', 'i')
                ->innerJoin('i.interlocutorCenters', 'cn')
                ->innerJoin('cn.center', 'cntr')
                ->innerJoin('cntr.project', 'cnpr')
                ->innerJoin('cnpr.userProjects', 'u')
                ->andWhere('u.user = :user_id')
                ->andWhere('u.disabledAt IS NULL')
                ->andWhere('i.deletedAt IS NULL')
                ->setParameter('user_id', $user->getId());
        }

        // Filter portee
        if (!empty($query['filter']['portee']) && 0 < (int) $query['filter']['portee']) {
            $qb->andWhere('a.porteeDocument = :portee');
            $qb->setParameter('portee', (int) $query['filter']['portee']);
        }

        // Filter type
        if (!empty($query['filter']['type']) && 0 < (int) $query['filter']['type']) {
            $qb->andWhere('a.TypeDocument = :type');
            $qb->setParameter('type', (int) $query['filter']['type']);
        }

        // Filter Status
        if (!empty($query['filter']['status']) && 0 < (int) $query['filter']['status']) {
            $status = (int) $query['filter']['status'];
            $is_valid = 1 === $status;

            $qb->andWhere('a.isValid = :status');
            $qb->setParameter('status', $is_valid);
        }

        if (!empty($query['order']) && '' !== $query['order']) {
            $sort = !empty($query['sort']) && in_array($query['sort'], ['asc', 'desc']) ? $query['sort'] : 'asc';
            $qb->orderBy('a.'.$query['order'], $sort);
        }

        $q = $qb->getQuery();

        return $q->getResult();
    }
}
