<?php

namespace App\ESM\Repository;

use App\ESM\Entity\AuditTrail\ProjectAuditTrail;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Entity\UserProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(): QueryBuilder
	{
        return $this->createQueryBuilder('p')
            ->innerJoin('p.sponsor', 's')
            ->innerJoin('p.responsiblePM', 'r')
            ;
    }

	/**
	 * @param $type
	 * @return QueryBuilder
	 */
    public function indexListGenAuditProject($type): QueryBuilder
	{
        return  $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(ProjectAuditTrail::class, 'p')
            ->andWhere('p.modif_type = :type')
            ->setParameter('type', $type);
    }

	/**
	 * @param $type
	 * @return QueryBuilder
	 */
    public function indexListGenAuditProjectArchive($type): QueryBuilder
	{
        return  $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(ProjectAuditTrail::class, 'p')
            //->andWhere("p.modif_type = :type AND ( p.details LIKE '%closedAt%' OR p.details LIKE '%closeDemandAt%')")
            ->andWhere('p.modif_type = :type')
            ->setParameter('type', $type);
    }

	/**
	 * @param $type
	 * @return QueryBuilder
	 */
    public function indexListGenAuditProjectUpdate($type): QueryBuilder
	{
        return  $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(ProjectAuditTrail::class, 'p')
            //->andWhere("p.modif_type = :type and p.details LIKE '%update_at%'")
            ->andWhere('p.modif_type = :type')
            ->setParameter('type', $type);
    }

	/**
	 * @param $id
	 * @return QueryBuilder
	 */
    public function indexListGenProject($id): QueryBuilder
	{
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('*')
            ->from(Project::class, 'p')
            ->innerJoin(UserProject::class, 'up')
            ->innerJoin(User::class, 'u')
            ->andWhere('u.id = :id and up.disabledAt IS NULL')
            ->setParameter('id', $id)
        ;

        return $qb;
    }

    /**
     * @param $projectId
     *
     * @return QueryBuilder
     */
    public function getCountriesByProject($projectId): QueryBuilder
	{
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c.name')
            ->from(Project::class, 'p')
            ->innerJoin('p.countries', 'c')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId);
    }

	/**
	 * @return array
	 */
	public function getAllID(): array
	{
		$projects = [];

		$result = $this->createQueryBuilder('project')
			->select('project.id', 'project.acronyme')
			->getQuery()->getArrayResult();

		foreach ($result as $key => $value) {$projects[$value['id']] = $value['acronyme'];}

		return $projects;
	}
}
