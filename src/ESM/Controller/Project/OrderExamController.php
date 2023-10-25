<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\Entity\Exam;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderExamController
 * @package App\ESM\Controller\Project
 */
class OrderExamController extends AbstractController
{
    /**
     * @Route("/projects/{id}/exam-settings/order", name="project.exam.setting.order", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_CREATE')")
     */
    public function index(Project $project): Response
    {
        $exams = $this->getDoctrine()->getManager()->getRepository(Exam::class)->findBy(['project' => $project, 'deletedAt' => null], ['position' => 'ASC']);

        return $this->json(array_map(function ($exam) {
            return [
                'id' 		=> $exam->getId(),
                'name' 		=> $exam->getName(),
                'order' 	=> $exam->getOrdre(),
                'position' 	=> $exam->getPosition(),
            ];
        }, $exams));
    }

	/**
	 * @Route("/projects/{id}/exam-settings/order/edit", name="project.exam.setting.order.edit", methods="PUT", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_CREATE')")
	 * @throws \JsonException
	 */
    public function edit(Project $project, Request $request): Response
    {
		$em 	= $this->getDoctrine()->getManager();
        $data 	= json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($data) {
            foreach ($data['data'] as $exam) {
                $entity = $em->getRepository(Exam::class)->find($exam['id']);
                $entity->setPosition($exam['position']);

                $em->persist($entity);
                $em->flush();
            }
        }

        $status = 1;
        $msg = 'Ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
}
