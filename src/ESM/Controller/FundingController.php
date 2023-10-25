<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Funding;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\FundingHandler;
use App\ESM\ListGen\FundingList;
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
 * Class FundingController
 * @package App\ESM\Controller
 */
class FundingController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/projects/{id}/show/fundings", name="project.list.fundings", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('FUNDING_LIST')")
     *
     * @return Response
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project)
    {
        $list = $lgm->getListGen(FundingList::class);

        return $this->render('funding/index.html.twig', [
            'project' => $project,
            'list'    => $list->getList($request->getLocale(), $project),
        ]);
    }

    /**
     * @Route("/project/{id}/ajax/fundings", name="project.funding.index.ajax", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('FUNDING_LIST')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project)
    {
        $list = $lgm->getListGen(FundingList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

	/**
	 * @Route("/projects/{id}/show/fundings/new", name="project.fundings.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECT_WRITE', project) and is_granted('FUNDING_CREATE')")
	 *
	 * @param Request $request
	 * @param FundingHandler $fundingHandler
	 * @param Project $project
	 * @return RedirectResponse|Response
	 */
    public function new(Request $request, FundingHandler $fundingHandler, Project $project)
    {
        $funding = new Funding();
        $funding->setProject($project);

        if ($fundingHandler->handle($request, $funding)) {
            return $this->redirectToRoute('project.list.fundings', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('funding/create.html.twig', [
            'form' => $fundingHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

	/**
	 *
	 * @Route("/projects/{id}/fundings/{idFunding}/edit", name="project.fundings.edit", requirements={"id"="\d+",  "idFunding":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("funding", options={"id"="idFunding"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('FUNDING_EDIT', funding)")
	 *
	 * @param Request $request
	 * @param FundingHandler $fundingHandler
	 * @param Project $project
	 * @param Funding $funding
	 * @return Response
	 */
    public function edit(Request $request, FundingHandler $fundingHandler, Project $project, Funding $funding): Response
    {
        if ($fundingHandler->handle($request, $funding)) {
            return $this->redirectToRoute('project.list.fundings', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('funding/create.html.twig', [
            'form' => $fundingHandler->createView(),
            'project' => $project,
            'edit' => true,
        ]);
    }

	/**
	 * @Route("/projects/{id}/fundings/{idFunding}/show", name="project.fundings.show", requirements={"id"="\d+", "idFunding":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("funding", options={"id"="idFunding"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('FUNDING_SHOW', funding)")
	 *
	 * @param Project $project
	 * @param Funding $funding
	 * @return Response
	 */
    public function show(Project $project, Funding $funding): Response
    {
        // render
        return $this->render('funding/show.html.twig', [
            'funding' => $funding,
            'project' => $project,
        ]);
    }

	/**
	 *
	 * @Route("/projects/{id}/fundings/{idFunding}/archive", name="project.fundings.archive", requirements={"id"="\d+", "idFunding":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("funding", options={"id"="idFunding"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('FUNDING_ARCHIVE', funding)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param Funding $funding
	 * @return RedirectResponse
	 */
    public function archive(Request $request, Project $project, Funding $funding): RedirectResponse
    {
        $funding->setDeletedAt(new \DateTime());
        $this->entityManager->persist($funding);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.fundings', [
            'id' => $request->get('id'),
        ]);
    }

	/**
	 *
	 * @Route("/projects/{id}/fundings/{idFunding}/restore", name="project.fundings.restore", requirements={"id"="\d+", "idFunding":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("funding", options={"id"="idFunding"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('FUNDING_RESTORE', funding)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param Funding $funding
	 * @return Response
	 */
    public function restore(Request $request, Project $project, Funding $funding): Response
    {
        $funding->setDeletedAt(null);
        $this->entityManager->persist($funding);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.fundings', [
            'id' => $request->get('id'),
        ]);
    }
}
