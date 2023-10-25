<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\ProjectTrailTreatment;
use App\ESM\Entity\User;
use App\ESM\ListGen\ProjectUserList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class ProjectController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="projects.index")
     */
    public function index(Request $request, ListGenFactory $lgm): Response
    {
        $list = $lgm->getListGen(ProjectUserList::class);

        return $this->render('project/index.html.twig', [
            'list' => $list->getList($this->getUser(), $request->getLocale()),
            'home' => true,
        ]);
    }

    /**
     * @Route("/ajax/projects", name="project.index.ajax")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());

        // listgen handle request
        $list = $lgm->getListGen(ProjectUserList::class);
        $list = $list->getList($user, $request->getLocale());

        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

    /**
     * @Route("/{id}/show", name="project.show", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project)")
	 * @ParamConverter("project", options={"id"="id"})
     *
     * @return Response
     */
    public function showProject(Project $project)
    {
        // render
        return $this->render('project/home.html.twig', [
            'project' => $project,
            'homeShow' => true,
            'action' => 'show',
        ]);
    }

	/**
	 * @Route("/{id}/show/file", name="project.show.file", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project)")
	 * @ParamConverter("project", options={"id"="id"})
	 * @param Project $project
	 * @return Response
	 */
    public function showFileProject(Project $project): Response
	{
        $trailTreatments = $this->entityManager->getRepository(ProjectTrailTreatment::class)->findBy(['project' => $project]);
        // render
        return $this->render('project/file.html.twig', [
            'project' => $project,
            'trail_treatments' => $trailTreatments,
            'projectShow' => true,
            'file' => true,
            'action' => 'show',
        ]);
    }

}
