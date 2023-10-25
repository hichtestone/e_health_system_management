<?php

declare(strict_types=1);

namespace App\ESM\Repository;

use App\ESM\Entity\Application;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function canGiveToken(string $api_token, int $user_id): bool
    {
        $q = $this->createQueryBuilder('a')
            ->innerJoin('a.users', 'u')
            ->select('COUNT(1)')
            ->where('a.api_token = :api_token')
            ->andWhere('u.id = :user_id')
            ->setParameter('api_token', $api_token)
            ->setParameter('user_id', $user_id)
            ->getQuery();

        return $q->getSingleScalarResult() > 0;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasUserAccess(User $user, Application $application): bool
    {
        $q = $this->createQueryBuilder('a')
            ->innerJoin('a.users', 'u')
            ->select('COUNT(1)')
            ->where('a = :application')
            ->andWhere('u = :user')
            ->setParameter('application', $application)
            ->setParameter('user', $user)
            ->getQuery();

        return $q->getSingleScalarResult() > 0;
    }

    /**
     * get user's applications ordered by name.
     *
     * @return mixed
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('a')
                    ->leftJoin('a.users', 'u')
                    ->where('u = :user')
                    ->andWhere('a.deleted_at IS NULL')
                    ->orderBy('a.name', 'ASC')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexListGen()
    {
        return $this->createQueryBuilder('a')
                    ->innerJoin('a.type', 't')
            ->where('a.deleted_at IS NULL')
            ;
    }
}
