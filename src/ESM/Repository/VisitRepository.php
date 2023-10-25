<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Project;
use App\ESM\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Visit::class);
        $this->security = $security;
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(Project $project)
    {


        $is_granted_archive = $this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE');

        $qb = $this->createQueryBuilder('v')
            ->innerJoin('v.phase', 'ph')
            ->leftJoin('v.patientVariable', 'pv')
            ->innerJoin('v.project', 'p')
            ->where('p.id = :project');

        // Le profil n'a pas le role "Création/Edition/Archivage schéma visite"
        if (!$is_granted_archive) {
            $qb->andWhere('v.deletedAt IS NULL');
        }

        $qb
            ->setParameter('project', $project->getId())
            ->orderBy('v.position', 'ASC');

        return $qb;
    }

    /**
     * @return int|mixed|string
     */
    public function listVisit(Project $project)
    {
        return $this->createQueryBuilder('v')
            ->innerJoin('v.patientVariable', 'pv')
            ->innerJoin('pv.variableType', 'vt')
            ->innerJoin('pv.project', 'p')
            ->where('p.id = :project')
            ->setParameter('project', $project->getId())
            ->getQuery()
            ->getResult();
    }


    public function getPatientVariableByVisit($idVisit)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT patient_variable.id
            FROM visit_variable 
            INNER JOIN visit ON visit_variable.visit_id = visit.id 
            INNER JOIN patient_variable ON visit_variable.patient_variable_id = patient_variable.id
            WHERE visit.id  = :idVisit;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idVisit' => $idVisit]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

    public function findByProject(Project $project)
    {
        return $this->createQueryBuilder('v')
            ->innerJoin('v.phase', 'p')
            ->where('p.project = :project')
            ->setParameter('project', $project)
            ->orderBy('p.label')
            ->addOrderBy('v.position')
            ->andWhere('p.deletedAt IS NULL')
            ->andWhere('v.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getVisitVariable($idVisit)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT visit_variable.patient_variable_id
            FROM visit_variable 
            INNER JOIN visit ON visit_variable.visit_id = visit.id 
            WHERE visit.id  = :idVisit;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idVisit' => $idVisit]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

	public function getCountVisit($projectId, $name)
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT count(visit.id) as counter
            FROM visit 
            WHERE visit.short  = :name
			 AND visit.project_id = :projectId
             ';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['projectId' => $projectId, 'name' => $name]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchOne();
	}

}
