<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Center;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Center|null find($id, $lockMode = null, $lockVersion = null)
 * @method Center|null findOneBy(array $criteria, array $orderBy = null)
 * @method Center[]    findAll()
 * @method Center[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Center::class);
    }

	/**
	 * @param Project $project
	 * @param bool $displayArchived
	 * @return QueryBuilder
	 */
    public function indexSelectionListGen(Project $project, bool $displayArchived): QueryBuilder
	{
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.institutions', 'i')
            ->innerJoin('c.centerStatus', 'cs')
            ->innerJoin('i.country', 'p')
            ->where('c.project = :project')
            ->andWhere('cs.type IN (1)')
            ->setParameter('project', $project);

        return $displayArchived ? $query : $query->andWhere('c.deletedAt IS NULL');
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
    public function indexCenterListGen(Project $project): QueryBuilder
	{
        return $this->createQueryBuilder('c')
            ->innerJoin('c.institutions', 'i')
            ->innerJoin('c.centerStatus', 'cs')
            ->innerJoin('i.country', 'p')
            ->where('c.project = :project')
            ->andWhere('cs.type IN (2,3,4)')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('project', $project);
    }

	/**
	 * @param Project $project
	 * @return QueryBuilder
	 */
    public function indexListGenProjectInterlocutor(Project $project): QueryBuilder
	{
        return $this->getEntityManager()->createQueryBuilder()
            //->select('c.name')
            ->from(Center::class, 'c')
            ->innerJoin('c.centerStatus', 'cs')
            ->leftJoin('c.interlocutors', 'i')
            // civility => sexe s
            ->leftJoin('i.civility', 's')
            ->andWhere("c.project = :project and cs.type = true and cs.label = 'Sélectionné'")
            ->setParameter('project', $project);
    }

	/**
	 * @param Project $project
	 * @param Interlocutor $interlocutor
	 * @return int|mixed|string
	 */
    public function findByInterlocutorProject(Project $project, Interlocutor $interlocutor)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.interlocutorCenters', 'ic')
            ->where('ic.interlocutor = :interlocutor')
            ->setParameter('interlocutor', $interlocutor)
            ->andWhere('c.project = :project')
            ->setParameter('project', $project)
            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

	/**
	 * @param Project $project
	 * @param array $countryIds
	 * @return int|mixed|string
	 */
    public function findByProjectNotInCountries(Project $project, array $countryIds)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.institutions', 'i')
            ->innerJoin('i.country', 'co')
            ->where('c.project = :project')
            ->setParameter('project', $project)
            ->andWhere('co.id NOT IN (:ids)')
            ->setParameter('ids', $countryIds)
            ->getQuery()
            ->getResult();
    }

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$centers = [];

		$result = $this->createQueryBuilder('center')
			->select('center.id', 'center.name')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$centers[$value['id']] = $value['name'];}

		return $centers;
	}
}
