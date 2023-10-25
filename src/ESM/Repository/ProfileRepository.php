<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * liste admin profile.
     *
     * @return QueryBuilder
     */
    public function indexListGen()
    {
        $qb = $this->createQueryBuilder('p');

        return $qb;
    }

    public function findAllUsersCanCloseDemand($code): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT DISTINCT(profile.name)
            FROM profile
            INNER JOIN profile_role ON profile.id = profile_role.profile_id 
            INNER JOIN role ON profile_role.role_id = role.id 
            WHERE role.code = :code';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['code' => $code]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }
}
