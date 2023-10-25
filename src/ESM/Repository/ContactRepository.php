<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGenProjectContact($idProject)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.type', 't')
            ->leftJoin('c.phase', 'ph')
            ->leftJoin('c.typeRecipient', 'r')
            ->leftJoin('c.object', 'o')
            ->leftJoin('c.interlocutors', 'i')
            ->leftJoin('c.intervenants', 'u')
            // emitteur e
            ->leftJoin('c.intervenant', 'e')
            ->leftJoin('c.project', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $idProject);
    }

    /**
     * @return int|mixed|string
     */
    public function findByInterlocutorProject(Interlocutor $interlocutor, Project $project)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.interlocutors', 'i')
            ->where('i.id = :interlocutor')
            ->setParameter('interlocutor', $interlocutor)
            ->andWhere('c.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function findByIntntervenantProject(User $user, Project $project)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.intervenants', 'i')
            ->where('i.id = :user')
            ->setParameter('user', $user)
            ->andWhere('c.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }

	/**
	 * @param Contact $contact
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function findUserIds(Contact $contact): array
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT user.id 
			FROM `contact_user` 
				INNER JOIN contact ON contact_user.contact_id = contact.id 
				INNER JOIN user ON contact_user.user_id = user.id 
			WHERE contact.id = :contactId';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['contactId' =>  $contact->getId()]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchAllAssociative();
	}

	/**
	 * @param Contact $contact
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function findInterlocutorIds(Contact $contact): array
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT interlocutor.id 
			FROM `contact_interlocutor` 
				INNER JOIN contact ON contact_interlocutor.contact_id = contact.id 
				INNER JOIN interlocutor ON contact_interlocutor.interlocutor_id = interlocutor.id 
			WHERE contact.id = :contactId';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['contactId' =>  $contact->getId()]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchAllAssociative();
	}

}
