<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Contact;
use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\DocumentTrackingInterlocutor;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\DocumentTrackingInterlocutorHandler;
use App\ESM\FormHandler\InterlocutorHandler;
use App\ESM\ListGen\InterlocutorCenterList;
use App\ESM\Repository\InterlocutorCenterRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/projects")
 */
class InterlocutorController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/interlocutors", name="project.list.interlocutors", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECTINTERLOCUTOR_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen
        $list = $lgm->getListGen(InterlocutorCenterList::class);

        return $this->render('interlocutor/index.html.twig', [
            'list' => $list->getList($request->getLocale(), $project),
        ]);
    }

    /**
     * @Route("/{id}/ajax/interlocutors", name="project.interlocutors.index.ajax", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECTINTERLOCUTOR_LIST')")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen handle request
        $list = $lgm->getListGen(InterlocutorCenterList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/interlocutor/{idInterlocutor}/show", name="project.interlocutor.show", requirements={"id"="\d+", "idInterlocutor":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("interlocutor", options={"id"="idInterlocutor"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PROJECTINTERLOCUTOR_SHOW', interlocutor)")
	 *
	 * @param Project $project
	 * @param Interlocutor $interlocutor
	 * @param InterlocutorCenterRepository $interlocutorCenterRepository
	 * @return Response
	 */
    public function show(Project $project, Interlocutor $interlocutor, InterlocutorCenterRepository $interlocutorCenterRepository): Response
	{
        $interlocutorCenters = $interlocutorCenterRepository->findByInterlocutorProject($interlocutor, $project);
        $contacts = $this->getDoctrine()->getRepository(Contact::class)->findByInterlocutorProject($interlocutor, $project);
        $documentTrackings = $this->getDoctrine()->getRepository(DocumentTrackingInterlocutor::class)
            ->findBy(['interlocutor' => $interlocutor]);

        // render
        return $this->render('interlocutor/show.html.twig', [
            'interlocutor' => $interlocutor,
            'interlocutorCenters' => $interlocutorCenters,
            'contacts' => $contacts,
            'documentTrackings' => $documentTrackings,
            'jobNoRpps' => InterlocutorHandler::jobNoRpps,
            'jobInv' => InterlocutorHandler::jobInv,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/interlocutor/{idInterlocutor}/document-tracking/{idDocumentTracking}/edit", name="project.interlocutor.trackingDocument.edit", requirements={"id"="\d+", "idInterlocutor":"\d+", "idDocumentTracking"="\d+"})
     * @ParamConverter("interlocutor", options={"id"="idInterlocutor"})
     * @ParamConverter("documentTrackingInterlocutor", options={"id"="idDocumentTracking"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECTINTERLOCUTOR_EDIT', interlocutor)")
     */
    public function documentTrackingEdit(Request $request, Project $project, Interlocutor $interlocutor, DocumentTrackingInterlocutor $documentTrackingInterlocutor, DocumentTrackingInterlocutorHandler $formHandler, RouterInterface $router)
    {
        if ($formHandler->handle($request, $documentTrackingInterlocutor, [
            'project' => $project,
            'isCreating' => false,
            'interlocutor' => $interlocutor,
        ])) {
            return $this->redirect($request->headers->get('referer'));
            //return $this->redirectToRoute('project.interlocutor.show', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('documentTracking/document_form.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'edit',
                'url' => $router->generate('project.interlocutor.trackingDocument.edit', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId(), 'idDocumentTracking' => $documentTrackingInterlocutor->getId()]),
                'formHelperUrl' => $router->generate('project.interlocutor.trackingDocument.help', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/{id}/interlocutor/{idInterlocutor}/document-tracking/new", name="project.interlocutor.trackingDocument.new", requirements={"id"="\d+", "idInterlocutor":"\d+"})
     * @ParamConverter("interlocutor", options={"id"="idInterlocutor"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECTINTERLOCUTOR_EDIT', interlocutor)")
     */
    public function newDoc(Request $request, Project $project, Interlocutor $interlocutor, DocumentTrackingInterlocutorHandler $formHandler, RouterInterface $router)
    {
        $documentTrackingInterlocutor = new DocumentTrackingInterlocutor();
        $documentTrackingInterlocutor->setInterlocutor($interlocutor);

        if ($formHandler->handle($request, $documentTrackingInterlocutor, [
            'project' => $project,
            'isCreating' => true,
            'interlocutor' => $interlocutor,
        ])) {
            return $this->redirectToRoute('project.interlocutor.show', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('documentTracking/document_form.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'create',
                'url' => $router->generate('project.interlocutor.trackingDocument.new', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId()]),
                'formHelperUrl' => $router->generate('project.interlocutor.trackingDocument.help', ['id' => $project->getId(), 'idInterlocutor' => $interlocutor->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/{id}/interlocutor/{idInterlocutor}/document-tracking/help", name="project.interlocutor.trackingDocument.help", requirements={"id"="\d+", "idInterlocutor":"\d+"})
     * @ParamConverter("interlocutor", options={"id"="idInterlocutor"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECTINTERLOCUTOR_EDIT', interlocutor)")
     */
    public function docHelper(Request $request, Project $project, Interlocutor $interlocutor)
    {
        $documentTrackingId = $request->get('documentTracking');
        if ('' != $documentTrackingId) {
            $documentTracking = $this->getDoctrine()->getRepository(DocumentTracking::class)->find($documentTrackingId);
        }

        $toBeSent = false;
        $toBeReceived = false;
        if (null !== $documentTracking) {
            $toBeSent = $documentTracking->isToBeSent();
            $toBeReceived = $documentTracking->isToBeReceived();
        }

        return $this->json([
            'toBeSent' => $toBeSent,
            'toBeReceived' => $toBeReceived,
        ]);
    }

    public function interlocutor_document_transverse(int $id)
    {
        /** @var Interlocutor $interlocutor */
        $interlocutor = $this->getDoctrine()->getRepository(Interlocutor::class)->find($id);

        return $this->render('interlocutor/document_transverse.html.twig', [
            'interlocutor' => $interlocutor,
        ]);
    }
}
