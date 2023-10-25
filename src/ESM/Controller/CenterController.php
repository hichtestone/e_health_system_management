<?php

namespace App\ESM\Controller;

use App\ESM\Entity\AuditTrail\CenterAuditTrail;
use App\ESM\Entity\Center;
use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\DocumentTrackingCenter;
use App\ESM\Entity\Institution;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\Project;
use App\ESM\Form\CenterStatusMassType;
use App\ESM\FormHandler\CenterHandler;
use App\ESM\FormHandler\DocumentTrackingCenterHandler;
use App\ESM\FormHandler\InstitutionHandler;
use App\ESM\ListGen\CenterList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class CenterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 * @Route("/{id}/center", name="project.center.index", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('MENU_CENTER')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        $list = $lgm->getListGen(CenterList::class);

        return $this->render('center/index.html.twig', [
            'project' => $project,
            'list' => $list->getList($project),
        ]);
    }

	/**
	 * @Route("/{id}/center/ajax", name="project.center.index.ajax", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CENTER_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        $list = $lgm->getListGen(CenterList::class);
        $list = $list->getList($project);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/center/{idCenter}/show", name="project.center.show", requirements={"id"="\d+", "idCenter":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("center", options={"id"="idCenter"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CENTER_SHOW', center)")
	 *
	 * @param Project $project
	 * @param Center $center
	 * @return RedirectResponse|Response
	 */
    public function show(Project $project, Center $center)
    {
        if (1 === $center->getCenterStatus()->getType()) {
            return $this->redirectToRoute('project.selection.show', ['id' => $project->getId(), 'idCenter' => $center->getId()]);
        }

        $interlocutorsCenter = $this->entityManager->getRepository(InterlocutorCenter::class)->findBy(['center' => $center]);
        $auditTrail 		 = $this->getDoctrine()->getRepository(CenterAuditTrail::class)->findBy(['entity' => $center], ['date' => 'ASC']);
        $documentTrackings 	 = $this->getDoctrine()->getRepository(DocumentTrackingCenter::class)->findBy(['center' => $center]);

		$institutions 	= $center->getInstitutions()->toArray();
		$institution 	= array_slice($institutions, count($institutions) - 1, 1);
		$isClosed 		= $center->getCenterStatus()->getType() != 4;

        return $this->render('center/show.html.twig', [
            'center' 				=> $center,
            'project' 				=> $project,
            'interlocutorsCenter' 	=> $interlocutorsCenter,
            'auditTrail' 			=> $auditTrail,
            'activeMenu2' 			=> 'center',
            'documentTrackings' 	=> $documentTrackings,
            'isClosed' 				=> $isClosed,
            'institutions' 			=> $institutions,
            'institution' 			=> $institution[0],
        ]);
    }

	/**
	 * @Route("/{id}/center/{idCenter}/edit", name="project.center.edit", requirements={"id"="\d+", "idCenter":"\d+"})
	 * @ParamConverter("center", options={"id"="idCenter"})
	 * @Security("is_granted('CENTER_EDIT', center)")
	 *
	 * @param Request $request
	 * @param CenterHandler $centerHandler
	 * @param Project $project
	 * @param Center $center
	 * @return Response
	 */
    public function edit(Request $request, CenterHandler $centerHandler, Project $project, Center $center): Response
	{
        if ($centerHandler->handle($request, $center, [
            'project' => $project,
            'isSelected' => true,
        ])) {
            return $this->redirectToRoute('project.center.show', [
                'id' => $project->getId(),
                'idCenter' => $center->getId(),
            ]);
        }

        return $this->render('center/create.html.twig', [
            'form' => $centerHandler->createView(),
            'project' => $project,
            'action' => 'edit',
            'activeMenu2' => 'center',
        ]);
    }

    /**
     * @Route("/{id}/center/status-mass/popup", name="project.center.attach_mass_popup", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_CREATE')")
     */
    public function statusMassPopup(Request $request, TranslatorInterface $translator, Project $project): Response
    {
        $params = json_decode($request->getContent(), true);

        $items = $params['items'] ?? [];
        if (count($items) > 0) {
            $items = array_map(function ($item) {
                return $item['id'];
            }, $items);

            $form = $this->createForm(CenterStatusMassType::class, [], [
                'status_type' => [2, 3],
                'action' => $this->generateUrl('project.center.attach_mass', ['id' => $project->getId()]),
            ]);
            $centers = $this->getDoctrine()->getRepository(Center::class)->findById($items);
            $form->get('centers')->setData($centers);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans('entity.Center.action.list'),
                    'html' => $this->renderView('center/status_mass_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'showCloseButton' => true,
                    'showConfirmButton' => false,
                ],
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'error' => 'No interlocutor selected',
            ]);
        }
    }

    /**
     * @Route("/{id}/center/status-mass/", name="project.center.attach_mass", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_CREATE')")
     */
    public function statusMass(Request $request, Project $project): Response
    {
        $form = $this->createForm(CenterStatusMassType::class, [], [
            'status_type' => [2, 3],
            'action' => $this->generateUrl('project.center.attach_mass', [
                'id' => $project->getId(),
            ]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $centers = $form->get('centers')->getData();
            $centerStatus = $form->get('centerStatus')->getData();

            foreach ($centers as $center) {
                if (null === $center->getDeletedAt()) {
                    $center->setCenterStatus($centerStatus);
                    $em->persist($center);
                }
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Success !');

            return $this->redirectToRoute('project.center.index', [
                'id' => $project->getId(),
            ]);
        }

        return $this->redirectToRoute('project.center.index', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/document-tracking/{idDocumentTracking}/edit", name="project.center.trackingDocument.edit", requirements={"id"="\d+", "idCenter":"\d+", "idDocumentTracking"="\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @ParamConverter("documentTrackingCenter", options={"id"="idDocumentTracking"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', center)")
     */
    public function documentTrackingEdit(Request $request, Project $project, Center $center, documentTrackingCenter $documentTrackingCenter, DocumentTrackingCenterHandler $formHandler, RouterInterface $router)
    {
        if ($formHandler->handle($request, $documentTrackingCenter, [
            'project' => $project,
            'isCreating' => false,
            'center' => $center,
        ])) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('documentTracking/document_form.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'edit',
                'url' => $router->generate('project.center.trackingDocument.edit', ['id' => $project->getId(), 'idCenter' => $center->getId(), 'idDocumentTracking' => $documentTrackingCenter->getId()]),
                'formHelperUrl' => $router->generate('project.center.trackingDocument.help', ['id' => $project->getId(), 'idCenter' => $center->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/document-tracking/new", name="project.center.trackingDocument.new", requirements={"id"="\d+", "idCenter":"\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', center)")
     */
    public function newDoc(Request $request, Project $project, Center $center, documentTrackingCenterHandler $formHandler, RouterInterface $router)
    {
        $documentTrackingCenter = new documentTrackingCenter();
        $documentTrackingCenter->setCenter($center);

        if ($formHandler->handle($request, $documentTrackingCenter, [
            'project' => $project,
            'isCreating' => true,
            'center' => $center,
        ])) {
            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $center->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('documentTracking/document_form.html.twig', [
                'form' => $formHandler->createView(),
                'action' => 'create',
                'url' => $router->generate('project.center.trackingDocument.new', ['id' => $project->getId(), 'idCenter' => $center->getId()]),
                'formHelperUrl' => $router->generate('project.center.trackingDocument.help', ['id' => $project->getId(), 'idCenter' => $center->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/document-tracking/help", name="project.center.trackingDocument.help", requirements={"id"="\d+", "idCenter":"\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', center)")
     */
    public function docHelper(Request $request, Project $project, Center $center): JsonResponse
	{
        $documentTrackingId = $request->get('documentTracking');
        if ('' !== $documentTrackingId) {
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

	/**
	 * @param int $id
	 * @return Response
	 */
    public function institution_document_transverse(int $id): Response
	{
        $institution = $this->getDoctrine()->getRepository(Institution::class)->find($id);

        return $this->render('center/document_transverse.html.twig', [
            'institution' => $institution,
        ]);
    }

	/**
	 * @Route("/{id}/center/{idCenter}/show/{institutionID}/institution", name="project.center.show.institution.show", requirements={"id"="\d+", "idCenter":"\d+", "institutionID"="\d+"})
	 * @ParamConverter("center", options={"id"="idCenter"})
	 * @ParamConverter("institution", options={"id"="institutionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project)")
	 *
	 * @param Project $project
	 * @param Institution $institution
	 * @return Response
	 */
	public function showInstitution(Project $project, Institution $institution): Response
	{
		return $this->render('project/institution/show.html.twig', [
			'institution' => $institution,
			'typeIdNoFiness' => InstitutionHandler::typeIdNoFiness,
		]);
	}
}
