<?php

namespace App\ESM\Repository;

use App\ESM\Entity\Exam;
use App\ESM\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Exam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exam[]    findAll()
 * @method Exam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exam::class);
    }

    /**
     * @return QueryBuilder
     */
    public function indexListGen(Project $project)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.type', 't')
            ->innerJoin('e.project', 'p')
            ->where('p.id = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('e.position', 'ASC');
    }

    /**
     * @return int|mixed|string
     */
    public function listExam(Project $project)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.patientVariable', 'pv')
            ->innerJoin('pv.variableType', 'vt')
            ->innerJoin('pv.project', 'p')
            ->where('p.id = :project')
            ->setParameter('project', $project->getId())
            ->getQuery()
            ->getResult();
    }

    public function getPatientVariableByExam($idExam)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT exam_variable.patient_variable_id
            FROM exam_variable 
            INNER JOIN exam ON exam_variable.exam_id = exam.id 
            INNER JOIN patient_variable ON exam_variable.patient_variable_id = patient_variable.id
            WHERE exam.id  = :idExam;
             ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idExam' => $idExam]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchOne();
    }

	public function getCountExam($projectId, $name)
	{
		$conn = $this->getEntityManager()->getConnection();

		$sql = 'SELECT count(exam.id) as counter
            FROM exam 
            WHERE exam.name  = :name
			AND exam.project_id = :projectId
             ';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['projectId' => $projectId, 'name' => $name]);

		// returns an array of arrays (i.e. a raw data set)
		return $stmt->fetchOne();
	}


}
