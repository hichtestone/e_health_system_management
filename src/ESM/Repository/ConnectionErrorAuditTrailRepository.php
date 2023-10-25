<?php

declare(strict_types=1);

namespace App\ESM\Repository;

use App\ESM\Entity\ConnectionErrorAuditTrail;
use App\ESM\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConnectionErrorAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConnectionErrorAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConnectionErrorAuditTrail[]    findAll()
 * @method ConnectionErrorAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectionErrorAuditTrailRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConnectionErrorAuditTrail::class);
    }

    public function indexListGen()
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
        ;

        return $qb;
    }

    /**
     * @return int|mixed|string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbFailedConnexion(User $user, int $seconds)
    {
        $q = $this->createQueryBuilder('e')
            ->select('COUNT(1)')
            ->where('e.created_at >= :datetime')
            ->andWhere('e.user = :user')
            ->setParameter('user', $user)
            ->setParameter('datetime', new DateTime("-$seconds second"))
            ->getQuery();

        return $q->getSingleScalarResult();
    }
}
