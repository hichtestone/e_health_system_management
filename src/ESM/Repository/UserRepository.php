<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Entity\UserProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\QueryBuilder;
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByLogin(string $username): ?User
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
        ;

        return $q->getOneOrNullResult();
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->from(User::class, 'u')
            ->leftJoin('u.civility', 'c')
            ->leftJoin('u.profile', 'p')
            ->leftJoin('u.job', 'j');
    }

    /**
     * @return QueryBuilder
     */
    public function indexArchiveListGen(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(User::class, 'u')
            ->leftJoin('u.civility', 'c')
            ->leftJoin('u.profile', 'p')
            ->andWhere('u.deletedAt IS NOT NULL');
    }

    /**
     * @param $id
     * @return QueryBuilder
     */
    public function indexListGenProject($id): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(User::class, 'u')
            ->innerJoin(UserProject::class, 'up')
            ->innerJoin(Project::class, 'p')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id);
    }

    /**
     * @param string $value
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function getListUsersCloseDemandByEmail(string $value): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT user.email, profile.name
            FROM user
            INNER JOIN profile ON user.profile_id = profile.id 
            WHERE profile.name = :value';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['value' => $value]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    /**
     * @return QueryBuilder
     */
    public function indexUserByProfile(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.profile', 'p')
            ->andWhere('p.name = :profil')
            ->setParameter('profil', 'Chef de Projet');
    }

    /**
     * @return QueryBuilder
     */
    public function getUnicancerArcCdpCecUsers()
    {
        $qb = $this->createQueryBuilder('user')
            ->leftJoin('user.society', 'society')
            ->leftJoin('user.job', 'userJob')
            ->andWhere('userJob.label IN (:labelJob)')
            ->andWhere('society.name =:society')
            ->setParameters([
                'labelJob' => ['ARC', 'CDP', 'CEC'],
                'society'  => 'UNICANCER'
            ]);

        return $qb->getQuery()->getResult();
    }

	/**
	 * @param array $roles
	 * @return int|mixed|string
	 */
	public function getUsersByRoles(array $roles)
	{
		$qb = $this->createQueryBuilder('user')
			->leftJoin('user.profile', 'profile')
			->leftJoin('profile.roles', 'roles')
			->andWhere('roles.code IN (:roles)')
			->setParameters([
				'roles' => $roles,
			]);

		return $qb->getQuery()->getResult();
    }
}
