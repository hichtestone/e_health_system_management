<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\Training;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Training|null find($id, $lockMode = null, $lockVersion = null)
 * @method Training|null findOneBy(array $criteria, array $orderBy = null)
 * @method Training[]    findAll()
 * @method Training[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Training::class);
    }

    public function indexListGenProjectTraining($id)
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.project', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    public function trainingListGen(Project $project)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.project', 'p')
            ->innerJoin('t.createdBy', 'u')
            ->where('t.project = :project')
            ->setParameter('project', $project)
        ;
    }

    public function findByUserProject(Project $project, User $user)
    {
        $q = $this->createQueryBuilder('t')
            ->innerJoin('t.users', 'u')
            ->where('u.id = :user_id')
            ->andWhere('t.project = :project')
            ->setParameter('user_id', $user->getId())
            ->setParameter('project', $project)
            ->getQuery()
        ;

        return $q->getResult();
    }
	/**
	 * @param Training $training
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function findUserIds(Training $training): array
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT user.id 
			FROM `training_user` 
				INNER JOIN training ON training_user.training_id = training.id 
				INNER JOIN user ON training_user.user_id = user.id 
			WHERE training.id = :trainingId';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['trainingId' =>  $training->getId()]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchAllAssociative();
	}

}
