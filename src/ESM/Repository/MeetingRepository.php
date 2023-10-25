<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Meeting;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Meeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meeting[]    findAll()
 * @method Meeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }

	/**
	 * @param $id
	 * @return QueryBuilder
	 */
    public function indexListGenProjectMeeting($id)
    {
        return  $this->createQueryBuilder('m')
            ->innerJoin('m.project', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
        ;
    }

	/**
	 * @param Project $project
	 * @param User $user
	 * @return int|mixed|string
	 */
    public function findByUserProject(Project $project, User $user)
    {
        $q = $this->createQueryBuilder('m')
            ->innerJoin('m.users', 'u')
            ->where('u.id = :user_id')
            ->andWhere('m.project = :project')
            ->setParameter('user_id', $user->getId())
            ->setParameter('project', $project)
            ->getQuery()
        ;

        return $q->getResult();
    }


	/**
	 * @param Meeting $meeting
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function findUserIds(Meeting $meeting): array
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT user.id 
			FROM `meeting_user` 
				INNER JOIN meeting ON meeting_user.meeting_id = meeting.id 
				INNER JOIN user ON meeting_user.user_id = user.id 
			WHERE meeting.id = :meetingId';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['meetingId' =>  $meeting->getId()]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchAllAssociative();
	}

}
