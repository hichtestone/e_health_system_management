<?php

namespace App\ESM\Controller;

use App\ESM\Entity\AuditTrail\CenterAuditTrail;
use App\ESM\Entity\Center;
use App\ESM\Entity\DocumentTrackingCenter;
use App\ESM\Entity\DropdownList\CenterStatus;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\Project;
use App\ESM\Form\CenterStatusMassType;
use App\ESM\FormHandler\CenterHandler;
use App\ESM\ListGen\SelectionList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class SelectionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 * @Route("/{id}/selection", name="project.selection.index", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CENTER_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        if ($this->isGranted('CENTER_LIST')) {
            $list = $lgm->getListGen(SelectionList::class);

            $displayArchived = in_array('ROLE_CENTER_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

            return $this->render('selection/index.html.twig', [
                'project' => $project,
                'list' => $list->getList($project, $displayArchived),
            ]);
        }
        if ($this->isGranted('PROJECTINTERLOCUTOR_LIST')) {
            return $this->redirectToRoute('project.list.interlocutors', ['id' => $project->getId()]);
        }
        if ($this->isGranted('DOCUMENTTRACKING_LIST')) {
            return $this->redirectToRoute('project.list.documentTracking', ['id' => $project->getId()]);
        }

        throw new AccessDeniedHttpException('Unauthorised');
    }

	/**
	 * @Route("/{id}/selection/ajax", name="project.selection.index.ajax", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CENTER_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        $displayArchived = in_array('ROLE_CENTER_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

        // listgen handle request
        $list = $lgm->getListGen(SelectionList::class);
        $list = $list->getList($project, $displayArchived);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/selection/{idCenter}/show", name="project.selection.show", requirements={"id"="\d+", "idCenter":"\d+"})
	 * @ParamConverter("center", options={"id"="idCenter"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CENTER_SHOW', center)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param Center $center
	 * @return Response
	 */
    public function show(Request $request, Project $project, Center $center): Response
	{
        if ($center->getCenterStatus()->getType() > 1) {
            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $center->getId()]);
        }

        $interlocutorsCenter = $this->entityManager->getRepository(InterlocutorCenter::class)->findBy([
            'center' => $center,
        ]);
        $auditTrail = $this->getDoctrine()->getRepository(CenterAuditTrail::class)
            ->findBy(['entity' => $center], ['date' => 'ASC']);
        $documentTrackings = $this->getDoctrine()->getRepository(DocumentTrackingCenter::class)
            ->findBy(['center' => $center]);

		$institutions = $center->getInstitutions()->toArray();

		$institution = array_slice($institutions, count($institutions) - 1, 1);

		$isClosed = $center->getCenterStatus()->getType() != 4;

        // render
        return $this->render('center/show.html.twig', [
            'center' => $center,
            'project' => $project,
			'isClosed' => $isClosed,
			'institutions' => $institutions,
			'institution' => $institution[0],
            'interlocutorsCenter' => $interlocutorsCenter,
            'auditTrail' => $auditTrail,
            'activeMenu2' => 'selection',
            'documentTrackings' => $documentTrackings,
        ]);
    }

	/**
	 * @Route("/{id}/selection/{idCenter}/edit", name="project.selection.edit", requirements={"id"="\d+",  "idCenter":"\d+"})
	 * @ParamConverter("center", options={"id"="idCenter"})
	 * @Security("is_granted('CENTER_EDIT', center)")
	 *
	 * @param Request $request
	 * @param CenterHandler $formHandler
	 * @param Project $project
	 * @param Center $center
	 * @return Response
	 */
    public function edit(Request $request, CenterHandler $formHandler, Project $project, Center $center): Response
	{
        if ($formHandler->handle($request, $center, [
            'project' => $project,
            'isSelected' => false,
        ])) {
            return $this->redirectToRoute('project.selection.show', [
                'id' => $project->getId(),
                'idCenter' => $center->getId(),
            ]);
        }

        return $this->render('center/create.html.twig', [
            'form' => $formHandler->createView(),
            'project' => $project,
            'action' => 'edit',
            'activeMenu2' => 'selection',
        ]);
    }

    /**
     * @Route("/{id}/selection/status-mass/popup", name="project.selection.attach_mass_popup", requirements={"id"="\d+"})
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
                'status_type' => [1, 2],
                'action' => $this->generateUrl('project.selection.attach_mass', ['id' => $project->getId()]),
            ]);
            $centers = $this->getDoctrine()->getRepository(Center::class)->findById($items);
            $form->get('centers')->setData($centers);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans('entity.Center.action.list'),
                    'html' => $this->renderView('selection/status_mass_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'showCloseButton' => true,
                    'showConfirmButton' => false,
                ],
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'error' => 'Aucun centre sélectionné',
            ]);
        }
    }

    /**
     * @Route("/{id}/selection/status-mass/", name="project.selection.attach_mass", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_CREATE')")
     */
    public function statusMass(Request $request, Project $project): Response
    {
        $form = $this->createForm(CenterStatusMassType::class, [], [
            'status_type' => [1, 2],
            'action' => $this->generateUrl('project.selection.attach_mass', [
                'id' => $project->getId(),
            ]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $centers = $form->get('centers')->getData();
            $centerStatus = $form->get('centerStatus')->getData();

            $nbFailed = [
                'no_pi' => [
                    'count' => 0,
                    'msg' => 'pas d\'investigateur principal',
                ],
            ];

            foreach ($centers as $center) {
                if (null === $center->getDeletedAt()) {
                    if (1 === $centerStatus->getType()
                        || count($center->getPrincipalInvestigators()) > 0) {
                        $center->setCenterStatus($centerStatus);
                        $em->persist($center);
                    } else {
                        ++$nbFailed['no_pi']['count'];
                    }
                }
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Success !');
            foreach ($nbFailed as $failure) {
                if ($failure['count'] > 0) {
                    $request->getSession()->getFlashBag()->add('warning', $failure['count'].' centre(s) non modifié(s): '.$failure['msg']);
                }
            }

            return $this->redirectToRoute('project.selection.index', [
                'id' => $project->getId(),
            ]);
        }

        return $this->redirectToRoute('project.selection.index', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 * @Route("/{id}/center/new", name="project.center.new", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_CREATE')")
	 *
	 * @param Request $request
	 * @param CenterHandler $formHandler
	 * @param Project $project
	 * @return Response
	 */
    public function new(Request $request, CenterHandler $formHandler, Project $project): Response
	{
        $center = new Center();

        $center->setProject($project);
        // Statut
        $status = $this->entityManager->getRepository(CenterStatus::class)->find(1);
        $center->setCenterStatus($status);

        if ($formHandler->handle($request, $center, [
            'project' => $project,
            'isSelected' => false,
            'isCreating' => true,
        ])) {
            return $this->redirectToRoute('project.selection.show', [
                'id' => $project->getId(),
                'idCenter' => $center->getId(),
            ]);
        }

        return $this->render('center/create.html.twig', [
            'form' => $formHandler->createView(),
            'project' => $project,
            'action' => 'create',
            'activeMenu2' => 'selection',
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/archive", name="project.center.archive", requirements={"id"="\d+", "idCenter":"\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('CENTER_ARCHIVE', center)")
     *
     * @return Response
     */
    public function archive(Project $project, Request $request, Center $center)
    {
        $center->setDeletedAt(new \DateTime());
        $this->entityManager->persist($center);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.center.show', [
            'id' => $project->getId(),
            'idCenter' => $center->getId(),
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/restore", name="project.center.restore", requirements={"id"="\d+", "idCenter":"\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('CENTER_RESTORE', center)")
     *
     * @return Response
     */
    public function restore(Project $project, Request $request, Center $center)
    {
        $center->setDeletedAt(null);
        $this->entityManager->persist($center);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.center.show', [
            'id' => $project->getId(),
            'idCenter' => $center->getId(),
        ]);
    }
}
