<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\Publication;
use App\ESM\FormHandler\PublicationHandler;
use App\ESM\ListGen\PublicationList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PublicationController
 * @package App\ESM\Controller
 */
class PublicationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/publications", name="project.list.publications", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PUBLICATION_LIST')")
     *
     * @return Response
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project)
    {
        $list = $lgm->getListGen(PublicationList::class);

        return $this->render('publication/index.html.twig', [
            'project' => $project,
            'list'    => $list->getList($request->getLocale(), $project),
        ]);
    }

    /**
     * @Route("/project/{id}/ajax/pulications", name="project.publication.index.ajax", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PUBLICATION_LIST')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project)
    {
        $list = $lgm->getListGen(PublicationList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

	/**
	 *
	 * @Route("/projects/{id}/publications/new", name="project.publications.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('PUBLICATION_CREATE')")
	 *
	 * @param Request $request
	 * @param PublicationHandler $publicationHandler
	 * @param Project $project
	 * @return RedirectResponse|Response
	 */
    public function new(Request $request, PublicationHandler $publicationHandler, Project $project)
    {
        $publication = new Publication();

        $publication->setProject($project);

        if ($publicationHandler->handle($request, $publication)) {
            return $this->redirectToRoute('project.list.publications', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('publication/create.html.twig', [
            'form'        => $publicationHandler->createView(),
            'projectShow' => true,
            'project'     => $project,
            'action'      => 'create',
        ]);
    }

	/**
	 * @Route("/projects/{id}/publications/{idPublication}/edit", name="project.publications.edit", requirements={"id"="\d+",  "idPublication":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("publication", options={"id"="idPublication"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('PUBLICATION_EDIT', publication)")
	 *
	 * @param Request $request
	 * @param PublicationHandler $publicationHandler
	 * @param Project $project
	 * @param Publication $publication
	 * @return RedirectResponse|Response
	 */
    public function edit(Request $request, PublicationHandler $publicationHandler, Project $project, Publication $publication)
    {
        if ($publicationHandler->handle($request, $publication)) {
            return $this->redirectToRoute('project.list.publications', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('publication/create.html.twig', [
            'form'    => $publicationHandler->createView(),
            'project' => $project,
            'action'  => 'update',
        ]);
    }

	/**
	 *
	 * @Route("/projects/{id}/publications/{idPublication}/show", name="project.publications.show", requirements={"id"="\d+", "idPublication":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("publication", options={"id"="idPublication"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PUBLICATION_SHOW', publication)")
	 *
	 *
	 * @param Project $project
	 * @param Publication $publication
	 * @return Response
	 */
    public function show(Project $project, Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
            'project'     => $project,
        ]);
    }

	/**
	 * @Route("/projects/{id}/publications/{idPublication}/archive", name="project.publication.archive", requirements={"id"="\d+", "idPublication":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("publication", options={"id"="idPublication"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('PUBLICATION_ARCHIVE', publication)")
	 *
	 * @param Project $project
	 * @param Publication $publication
	 * @return RedirectResponse
	 */
    public function archive(Project $project, Publication $publication): RedirectResponse
    {
        $publication->setDeletedAt(new \DateTime());
        $this->entityManager->persist($publication);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.publications.show', [
            'id' => $project->getId(),
            'idPublication' => $publication->getId(),
        ]);
    }

	/**
	 * @Route("/projects/{id}/publications/{idPublication}/restore", name="project.publication.restore", requirements={"id"="\d+", "idPublication":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("publication", options={"id"="idPublication"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('PUBLICATION_RESTORE', publication)")
	 *
	 * @param Project $project
	 * @param Request $request
	 * @param Publication $publication
	 * @return RedirectResponse
	 */
    public function restore(Project $project, Request $request, Publication $publication): RedirectResponse
    {
        $publication->setDeletedAt(null);
        $this->entityManager->persist($publication);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.publications.show', [
            'id'            => $project->getId(),
            'idPublication' => $publication->getId(),
        ]);
    }
}
