<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Meeting;
use App\ESM\Entity\Project;
use App\ESM\Entity\Training;
use App\ESM\Entity\User;
use App\ESM\Entity\UserProject;
use App\ESM\Form\UserType;
use App\ESM\ListGen\UserProjectList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserProjectController
 * @package App\ESM\Controller
 */
class UserProjectController extends AbstractController
{
    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/projects/{id}/users", name="project.user.list", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_PROJECT_INTERVENANT_READ')")
     *
     * @return Response
     */
    public function index(ListGenFactory $lgm, Project $project)
    {
        $list = $lgm->getListGen(UserProjectList::class);

        // render
        return $this->render('userProject/index.html.twig', [
            'list' => $list->getList($project),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/projects/{id}/users/ajax", name="project.user.list.ajax", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_PROJECT_INTERVENANT_READ')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project)
    {
        // listgen handle request
        $list = $lgm->getListGen(UserProjectList::class);
        $list = $list->getList($project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 *
	 * @Route("/projects/{id}/users/{idUser}/show", name="project.user.show", methods="GET", requirements={"id"="\d+",  "idUser":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("user", options={"id"="idUser"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_PROJECT_INTERVENANT_READ', user)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @return Response
	 */
    public function show(Project $project, Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($request->get('idUser'));
        $userProjects = $this->entityManager->getRepository(UserProject::class)->findBy(['user' => $request->get('idUser')]);

        $trainings = $this->entityManager->getRepository(Training::class)->findByUserProject($project, $user);

        $meetings = $this->entityManager->getRepository(Meeting::class)->findByUserProject($project, $user);

        $contacts = $this->getDoctrine()->getRepository(Contact::class)->findByIntntervenantProject($user, $project);

        // render
        return $this->render('userProject/show.html.twig', [
            'project' => $project,
            'user' => $user,
            'userProjects' => $userProjects,
            'userTrainings' => $trainings,
            'userMeetings' => $meetings,
            'contacts' => $contacts,
        ]);
    }

	/**
	 * @route("/projects/{id}/home", name="project.home.index", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 */
	public function project(Project $project): Response
	{
		$routes = [
			'ROLE_PROJECT_INTERVENANT_READ' =>  ['project.user.list', ['id' => $project->getId()]],
			'MEETING_LIST' => ['project.list.meetings', ['id' => $project->getId()]],
			'TRAINING_LIST' => ['project.list.trainings', ['id' => $project->getId()]],
			'CONTACT_LIST' => ['project.list.contacts', ['id' => $project->getId()]],
			'PUBLICATION_LIST' => ['project.list.publications', ['id' => $project->getId()]],
			'FUNDING_LIST' => ['project.list.fundings', ['id' => $project->getId()]],
			'DATE_SHOW' => ['project.list.dates', ['id' => $project->getId()]],
			'SUBMISSION_LIST' => ['project.list.submissions', ['id' => $project->getId()]],
			'RULE_SHOW' => ['project.list.rule', ['id' => $project->getId()]],
			'DEVIATION_REVIEW_LIST' => ['deviation.list', ['id' => $project->getId()]],
		];

		foreach ($routes as $role => $path) {
			if ($this->isGranted($role)) {
				return $this->redirectToRoute($path[0], $path[1]);
			}
		}

		return $this->redirectToRoute('project.show.file', ['id' => $project->getId()]);
	}

}
