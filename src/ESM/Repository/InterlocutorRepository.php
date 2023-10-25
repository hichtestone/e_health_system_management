<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Interlocutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interlocutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interlocutor[]    findAll()
 * @method Interlocutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterlocutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interlocutor::class);
    }

	/**
	 * liste admin interlocutor.
	 * @param bool $displayArchived
	 * @return QueryBuilder
	 */
    public function indexListGen(bool $displayArchived): QueryBuilder
	{
        $query = $this->createQueryBuilder('i')
            ->innerJoin('i.job', 'j');

        return $displayArchived ? $query : $query->andWhere('i.deletedAt IS NULL');
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
    public function indexListGenByProject(Project $project): QueryBuilder
	{
        return $this->createQueryBuilder('int')
            ->innerJoin('int.interlocutorCenters', 'ic')
            ->innerJoin('ic.center', 'c')
            ->innerJoin('c.project', 'p')
            ->leftJoin('int.job', 'j')
            ->andWhere('p.id = :idProject')
            ->andWhere('int.deletedAt IS NULL')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('idProject', $project->getId())
        ;
    }

    public function getInterlocutorsInstitutions()
    {
        return $this->createQueryBuilder('interlocutor')
            ->select('interlocutor.id AS interlocutor_id')
            ->addSelect('institution.id AS institution_id')
            ->innerJoin('interlocutor.institutions', 'institution')
            ->where('interlocutor.deletedAt IS NULL')
            ->andwhere('institution.deletedAt IS NULL')
            ->getQuery()
            ->getArrayResult();
    }

	/**
	 * @param $firstname
	 * @param $lastname
	 * @param $finess
	 * @return int|mixed|string
	 */
	public function getInterlocutorByInstitutionFiness($firstname, $lastname, $finess)
	{
		$qb = $this->createQueryBuilder('interlocutor');
		$qb->leftJoin('interlocutor.institutions', 'institutions');
		$qb->andWhere('interlocutor.firstName =:firstname');
		$qb->andWhere('interlocutor.lastName =:lastname');

		$orX = $qb->expr()->orX();
		$orX->add('institutions.finess =:finess');
		$orX->add('institutions.finessTmp =:finess');

		$qb->andWhere($orX);

		$qb->setParameters([
			'firstname' => $firstname,
			'lastname' 	=> $lastname,
			'finess' 	=> $finess
		]);

		$res = $qb->getQuery()->getResult();

		return $res;
	}
}
