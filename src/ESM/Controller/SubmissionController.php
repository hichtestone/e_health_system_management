<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\Submission;
use App\ESM\FormHandler\SubmissionHandler;
use App\ESM\ListGen\SubmissionList;
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
 * @Route("/projects")
 */
class SubmissionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/submissions", name="project.list.submissions", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SUBMISSION_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen
        $list = $lgm->getListGen(SubmissionList::class);

        return $this->render('submission/index.html.twig', [
            'list' => $list->getList($request->getLocale(), $project),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/submissions/ajax", name="project.submission.index.ajax", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SUBMISSION_LIST')")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen handle request
        $list = $lgm->getListGen(SubmissionList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/submissions/new", name="project.submission.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECT_WRITE', project) and is_granted('SUBMISSION_CREATE')")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param SubmissionHandler $submissionHandler
	 * @return Response
	 */
    public function create(Request $request, Project $project, SubmissionHandler $submissionHandler): Response
    {
        $submission = new Submission();
        $submission->setProject($project);

        if ($submissionHandler->handle($request, $submission, [
            'project' => $project,
        ])) {
            return $this->redirectToRoute('project.submission.show', [
                'id' => $project->getId(),
                'idSubmission' => $submission->getId(),
            ]);
        }

        return $this->render('submission/create.html.twig', [
            'form' => $submissionHandler->createView(),
            'edit' => false,
        ]);
    }

	/**
	 * @Route("/{id}/submissions/{idSubmission}/show", name="project.submission.show", requirements={"id"="\d+", "idSubmission":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("submission", options={"id"="idSubmission"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('SUBMISSION_SHOW', submission)")
	 *
	 * @param Project $project
	 * @param Submission $submission
	 * @return Response
	 */
    public function show(Project $project, Submission $submission): Response
    {
        // render
        return $this->render('submission/show.html.twig', [
            'project' => $project,
            'submission' => $submission,
        ]);
    }

	/**
	 *
	 * @Route("/{id}/submissions/{idSubmission}/edit", name="project.submission.edit", requirements={"id"="\d+",  "idSubmission":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("submission", options={"id"="idSubmission"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('SUBMISSION_EDIT', submission)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param SubmissionHandler $submissionHandler
	 * @param Submission $submission
	 * @return RedirectResponse|Response
	 */
    public function edit(Request $request, Project $project, SubmissionHandler $submissionHandler, Submission $submission)
    {
        if ($submissionHandler->handle($request, $submission, [
            'project' => $project,
        ])) {
            return $this->redirectToRoute('project.submission.show', [
                'id' => $project->getId(),
                'idSubmission' => $submission->getId(),
            ]);
        }

        return $this->render('submission/create.html.twig', [
            'form' => $submissionHandler->createView(),
            'project' => $project,
            'edit' => true,
        ]);
    }

	/**
	 *
	 * @Route("/{id}/submissions/{idSubmission}/archive", name="project.submission.archive", requirements={"id"="\d+", "idSubmission":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("submission", options={"id"="idSubmission"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECT_WRITE', project) and is_granted('SUBMISSION_ARCHIVE', submission)")
	 *
	 * @param Project $project
	 * @param Submission $submission
	 * @return RedirectResponse
	 */
    public function archive(Project $project, Submission $submission): RedirectResponse
    {
        $submission->setDeletedAt(new \DateTime());
        $this->entityManager->persist($submission);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.submission.show', [
            'id' => $project->getId(),
            'idSubmission' => $submission->getId(),
        ]);
    }

	/**
	 * @Route("/{id}/submissions/{idSubmission}/restore", name="project.submission.restore", requirements={"id"="\d+", "idSubmission":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("submission", options={"id"="idSubmission"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECT_WRITE', project) and is_granted('SUBMISSION_RESTORE', submission)")
	 *
	 * @param Project $project
	 * @param Submission $submission
	 * @return RedirectResponse
	 */
    public function restore(Project $project, Submission $submission): RedirectResponse
    {

		$submission->setDeletedAt(null);
		$this->entityManager->persist($submission);
		$this->entityManager->flush();


        return $this->redirectToRoute('project.submission.show', [
            'id' => $project->getId(),
            'idSubmission' => $submission->getId(),
        ]);
    }
}
