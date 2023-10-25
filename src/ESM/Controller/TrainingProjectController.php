<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\Training;
use App\ESM\FormHandler\TrainingHandler;
use App\ESM\ListGen\TrainingList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrainingProjectController
 * @package App\ESM\Controller
 */
class TrainingProjectController extends AbstractController
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @Route("/projects/{id}/trainings", name="project.list.trainings", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('TRAINING_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lg
	 * @param Project $project
	 *
	 * @return Response
	 */
	public function index(Request $request, ListGenFactory $lg, Project $project): Response
	{
		// listgen
		$list = $lg->getListGen(TrainingList::class);

		return $this->render('training/index.html.twig', [
			'list'    => $list->getList($request->getLocale(), $project),
			'project' => $project,
		]);
	}

	/**
	 *
	 * @Route("/{id}/trainings/ajax/", name="project.training.index.ajax")
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('TRAINING_LIST')")
	 *
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm): Response
	{
		$project = $this->entityManager->getRepository(Project::class)->find(['id' => $request->get('id')]);

		// listgen handle request
		$list = $lgm->getListGen(TrainingList::class);
		$list = $list->getList($request->getLocale(), $project);
		$list->setRequestParams($request->query);

		// json response
		return $list->generateResponse();
	}

	/**
	 * @Route("/projects/{id}/trainings/{idTraining}/show", name="project.training.show", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("training", options={"id"="idTraining"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('TRAINING_SHOW', training)")
	 *
	 * @param Project $project
	 * @param Training $training
	 * @return Response
	 */
	public function show(Project $project, Training $training): Response
	{
		// render
		return $this->render('training/show.html.twig', [
			'project'  => $project,
			'training' => $training,
		]);
	}

	/**
	 * @Route("/projects/{id}/trainings/new", name="project.training.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECT_WRITE', project) and is_granted('TRAINING_CREATE')")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param TrainingHandler $trainingHandler
	 * @return Response
	 */
	public function create(Request $request, Project $project, TrainingHandler $trainingHandler): Response
	{
		$training = new Training();
		$training->setProject($project);
		$training->setCreatedAt(new \DateTime());
		$training->setCreatedBy($this->getUser());

		$options = ['project' => $project, 'users' => []];

		if ($trainingHandler->handle($request, $training, $options)) {
			return $this->redirectToRoute('project.training.show', [
				'id'         => $project->getId(),
				'idTraining' => $training->getId(),
			]);
		}

		return $this->render('training/create.html.twig', [
			'form' => $trainingHandler->createView(),
			'edit' => false,
		]);
	}

	/**
	 * @Route("/projects/{id}/trainings/{idTraining}/edit", name="project.training.edit", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("training", options={"id"="idTraining"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('TRAINING_EDIT', training)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param TrainingHandler $trainingHandler
	 * @param Training $training
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function edit(Request $request, Project $project, TrainingHandler $trainingHandler, Training $training): Response
	{
		$users = $this->getTrainingUsers($training);

		$options = ['project' => $project, 'users' => $users];

		if ($trainingHandler->handle($request, $training, $options)) {
			return $this->redirectToRoute('project.list.trainings', [
				'id' => $project->getId(),
			]);
		}

		return $this->render('training/create.html.twig', [
			'form' => $trainingHandler->createView(),
			'edit' => true,
		]);
	}

	/**
	 * @Route("/projects/{id}/trainings/{idTraining}/delete", name="project.training.delete", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("training", options={"id"="idTraining"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('TRAINING_DELETE', training)")
	 *
	 * @param Project $project
	 * @param Training $training
	 * @return RedirectResponse
	 */
	public function delete(Project $project, Training $training): RedirectResponse
	{
		$this->entityManager->remove($training);
		$this->entityManager->flush();

		return $this->redirectToRoute('project.list.trainings', [
			'id' => $project->getId(),
		]);
	}

	/**
	 * @param Training $training
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception|Exception
	 */
	private function getTrainingUsers(Training $training): array
	{
		$usersIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$meetingUsers = $entityManager->getRepository(Training::class)->findUserIds($training);

		foreach ($meetingUsers as $meetingUser) {
			$usersIDs[] = $meetingUser['id'];
		}

		return $usersIDs;
	}

}
