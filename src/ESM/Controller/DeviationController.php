<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Center;
use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationAndSample;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\DropdownList\DeviationType;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Patient;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Form\DeviationCorrectionType;
use App\ESM\Form\DeviationDeclarationType;
use App\ESM\Form\DeviationReviewType;
use App\ESM\FormHandler\DeviationActionHandler;
use App\ESM\FormHandler\DeviationAndSampleHandler;
use App\ESM\FormHandler\DeviationCorrectionHandler;
use App\ESM\FormHandler\DeviationReviewHandler;
use App\ESM\ListGen\DeviationList;
use App\ESM\ListGen\DeviationReviewCrexList;
use App\ESM\ListGen\DeviationReviewList;
use App\ESM\Service\Deviation\DeviationService;
use App\ESM\Service\ListGen\ListGenFactory;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationController.
 *
 * @Route("/projects")
 */
class DeviationController extends AbstractController
{
	/**
	 * @Route("/{projectID}/deviation", name="deviation.list", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @Security("is_granted('DEVIATION_REVIEW_LIST')")
	 */
	public function list(Request $request, ListGenFactory $lgm, Project $project, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationList::class);

		return $this->render('deviation/list.html.twig', [
			'list'    => $list->getList($project, $translator),
			'project' => $project,
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/ajax", name="deviation.list.ajax", requirements={"projectID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @Security("is_granted('DEVIATION_REVIEW_LIST')")
	 */
	public function ListDeviationAjax(Request $request, ListGenFactory $lgm, Project $project, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationList::class);
		$list = $list->getList($project, $translator);
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/{projectID}/deviation/declaration/{deviationID}/{edit}", name="deviation.declaration", methods={"GET", "POST"}, defaults={"deviationID"=null, "edit"=null}, requirements={"projectID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_REVIEW_LIST')")
	 */
	public function declaration(Request $request, ListGenFactory $lgm, Project $project, Deviation $deviation = null, int $monitoring = null): Response
	{
		$fromMonitoring           = null !== $monitoring;
		$em                       = $this->getDoctrine()->getManager();
		$user                     = $this->getUser();
		$listDeviationCorrections = $em->getRepository(DeviationCorrection::class)->findBy(['project' => $project, 'deviation' => $deviation, 'deletedAt' => null]);
		$editMode                 = $request->get('edit');
		$deviationActions         = $this->getDoctrine()->getRepository(DeviationAction::class)->findBy(['deviation' => $deviation, 'isDone' => true, 'deletedAt' => null]);

		if (!$deviation && !$editMode) {
			$editMode = 'edit';
		}

		if (!$deviation) {
			$deviation = $this->createDeviation($project);
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'edit'        => $editMode
			]);
		}


		$form = $this->createForm(DeviationDeclarationType::class, $deviation, [
			'action'    => $this->generateUrl('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $deviation->getId(), 'edit' => 'edit']),
			'projectID' => $project->getId(),
			'editMode'  => $editMode,
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$deviation = $form->getData();

			$year = \date('Y');

			// change code
			if ($deviation->getCenter()) {
				$deviation->setCode('NC_' . $year . '_ESM_' . str_pad($deviation->getDeclaredBy()->getId(), 4, "0", STR_PAD_LEFT) . '_' . $project->getAcronyme() . '_' . $deviation->getCenter()->getNumber());
			} else {
				$deviation->setCode('NC_' . $year . '_ESM_' . str_pad($deviation->getDeclaredBy()->getId(), 4, "0", STR_PAD_LEFT) . '_' . $project->getAcronyme() . '_' . $deviation->getId());
			}

			// change status
			$deviation->setStatus(Deviation::STATUS_IN_PROGRESS);

			try {

				$em->persist($deviation);
				$em->flush();

			} catch (Exception $exception) {

				$this->addFlash('danger', 'erreur lors de enregistrement de la deviation !');

				return $this->render('deviation/declaration.html.twig', [
					'deviation'                => $deviation,
					'fromMonitoring'           => $fromMonitoring,
					'form'                     => $form->createView(),
					'editMode'                 => $editMode,
					'project'                  => $project,
					'listDeviationCorrections' => $listDeviationCorrections,
					'deviationActions'         => $deviationActions,
				]);
			}

			$this->addFlash('success', 'enregistrement de la deviation effectuée !');

			return $this->redirectToRoute('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $deviation->getId()]);
		}

		return $this->render('deviation/declaration.html.twig', [
			'deviation'                => $deviation,
			'fromMonitoring'           => $fromMonitoring,
			'form'                     => $form->createView(),
			'editMode'                 => $editMode,
			'project'                  => $project,
			'listDeviationCorrections' => $em->getRepository(DeviationCorrection::class)->findBy(['project' => $project, 'deviation' => $deviation, 'deletedAt' => null]),
			'deviationActions'         => $deviationActions,
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/declaration/action/new", name="deviation.declaration.action.new", requirements={"projectID"="\d+", "deviationID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_ACTION_CREATE_EDIT', deviation)")
	 */
	public function newAction(Request $request, Project $project, Deviation $deviation, DeviationActionHandler $deviationActionHandler, int $monitoring = null): Response
	{
		$deviationAction = new DeviationAction();

		$deviationAction->setDeviation($deviation);
		$deviationAction->setIsCrex(false);
		$deviationAction->setIsDone(true);

		$editMode = Deviation::STATUS_DRAFT === $deviation->getStatus() ? 'edit' : null;

		$options = [
			'project'      => $project,
			'deleteAction' => false,
			'intervener'   => [],
			'interlocutor' => [],
		];

		if ($deviationActionHandler->handle($request, $deviationAction, $options)) {
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'monitoring'  => $monitoring,
				'editMode'    => $editMode,
			]);
		}

		return $this->render('deviation/action/create.html.twig', [
			'form'      => $deviationActionHandler->createView(),
			'project'   => $project,
			'deviation' => $deviation,
			'action'    => 'create',
			'editMode'  => $editMode,
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/declaration/action/{actionID}/edit", name="deviation.declaration.action.edit", requirements={"projectID"="\d+", "deviationID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationAction", options={"id"="actionID"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) && is_granted('DEVIATION_ACTION_CREATE_EDIT', deviation)")
	 */
	public function editAction(Request $request, Project $project, Deviation $deviation, DeviationActionHandler $deviationActionHandler, DeviationAction $deviationAction, int $monitoring = null): Response
	{
		$options = [
			'project'      => $project,
			'deleteAction' => false,
			'intervener'   => $this->getDeviationActionUser($deviationAction),
			'interlocutor' => $this->getDeviationActionInterlocutor($deviationAction),
		];

		if ($deviationActionHandler->handle($request, $deviationAction, $options)) {
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'monitoring'  => $monitoring,
			]);
		}

		return $this->render('deviation/action/create.html.twig', [
			'form'            => $deviationActionHandler->createView(),
			'project'         => $project,
			'deviation'       => $deviation,
			'deviationAction' => $deviationAction,
			'action'          => 'edit',
			'editMode'        => null,
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/declaration/action/{actionID}/delete", name="deviation.declaration.action.delete", requirements={"projectID"="\d+", "deviationID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_ACTION_DELETE', deviation)")
	 */
	public function deleteAction(Request $request, Project $project, Deviation $deviation, DeviationAction $deviationAction, RouterInterface $router, DeviationActionHandler $deviationActionHandler, int $monitoring = null): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$deviationAction->setDeletedAt(new \DateTime());
		$entityManager->persist($deviationAction);

		$options = [
			'project'      => $project,
			'deleteAction' => true,
			'intervener'   => [],
			'interlocutor' => [],
		];


		if ($deviationActionHandler->handle($request, $deviationAction, $options)) {
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'monitoring'  => $monitoring,
			]);
		}

		return $this->json([
			'status' => 1,
			'html'   => $this->renderView('deviation/action/delete.html.twig', [
				'form'   => $deviationActionHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('deviation.declaration.action.delete', [
					'projectID'   => $project->getId(),
					'deviationID' => $deviation->getId(),
					'actionID'    => $deviationAction->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/declaration/brouillon/save/{deviationID}", name="deviation.declaration.brouillon.save", methods="POST",
	 *     requirements={"projectID"="\d+", "deviationID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_DRAFT_EDIT', deviation)")
	 */
	public function saveDraft(Request $request, Project $project, Deviation $deviation): Response
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		$data = json_decode($request->getContent(), true);

		try {
			$em = $this->getDoctrine()->getManager();

			if (in_array($data['fieldName'], ['ObservedAt', 'OccurenceAt'])) {
				$dateTimeField      = \DateTime::createFromFormat('d/m/Y', $data['fieldValue']);
				$data['fieldValue'] = $dateTimeField;
			}

			if ('DeclaredBy' === $data['fieldName']) {
				$declarant          = $em->getRepository(User::class)->find($data['fieldValue']);
				$data['fieldValue'] = $declarant;
			}

			if ('Type' === $data['fieldName'] || 'SubType' === $data['fieldName']) {
				if ($data['fieldValue'] && '' !== $data['fieldValue']) {
					$type               = $em->getRepository(DeviationType::class)->find($data['fieldValue']);
					$data['fieldValue'] = $type;
				} else {
					$data['fieldValue'] = null;
				}
			}

			if ('Center' === $data['fieldName']) {
				if ($data['fieldValue'] && '' !== $data['fieldValue']) {
					$center             = $em->getRepository(Center::class)->find($data['fieldValue']);
					$data['fieldValue'] = $center;
				} else {
					$data['fieldValue'] = null;
				}
			}

			if ('Institution' === $data['fieldName']) {
				if ($data['fieldValue'] && '' !== $data['fieldValue']) {
					$institution        = $em->getRepository(Institution::class)->find($data['fieldValue']);
					$data['fieldValue'] = $institution;
				} else {
					$data['fieldValue'] = null;
				}
			}

			if ('Patient' === $data['fieldName']) {
				if ($data['fieldValue'] && '' !== $data['fieldValue']) {
					$patient            = $em->getRepository(Patient::class)->find($data['fieldValue']);
					$data['fieldValue'] = $patient;
				} else {
					$data['fieldValue'] = null;
				}
			}

			if ('PotentialImpact' === $data['fieldName']) {
				if ('' === $data['fieldValue']) {
					$data['fieldValue'] = null;
				}
			}

			if ('Causality_0' === $data['fieldName'] || 'Causality_1' === $data['fieldName'] || 'Causality_2' === $data['fieldName']) {
				$causality   = $deviation->getCausality();
				$causalityID = substr($data['fieldName'], -1);

				if (!in_array($causalityID, $causality, true) && true === $data['fieldValue']) {
					$causality[] = $causalityID;
				} elseif (in_array($causalityID, $causality, true) && false === $data['fieldValue']) {
					unset($causality[array_search($causalityID, $causality, true)]);
				}

				$data['fieldName']  = 'Causality';
				$data['fieldValue'] = $causality;
			}

			$deviation->{'set' . $data['fieldName']}($data['fieldValue']);

			$em = $this->getDoctrine()->getManager();
			$em->persist($deviation);
			$em->flush();

			$responseStatus = 200;
			$responseData   = [
				'messageError'  => '',
				'messageStatus' => 'OK',
			];

		} catch (Exception $exception) {

			$responseStatus = 200;
			$responseData   = [
				'messageError'  => 'saving deviation error with ' . $data['fieldName'] . 'and value : ' . $data['fieldValue'],
				'messageStatus' => 'KO',
			];
		}

		return new JsonResponse($responseData, $responseStatus);
	}

	/**
	 * @Route("/{projectID}/deviation/brouillon/delete/{deviationID}", name="deviation.declaration.brouillon.delete", methods="POST",
	 *     requirements={"projectID"="\d+", "deviationID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_DRAFT_DELETE', deviation)")
	 */
	public function deleteDraft(Request $request, Project $project, Deviation $deviation)
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {

			return $this->json([
				'messageStatus' => 'OK',
				'html'          => $this->renderView('deviation/modal/deleteDraft.html.twig', []),
			], 200);

		} else {

			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($deviation);
				$em->flush();

				$responseStatus = 200;
				$responseData   = [
					'messageError'  => '',
					'messageStatus' => 'OK',
				];

			} catch (Exception $exception) {

				$responseStatus = 200;
				$responseData   = [
					'messageError'  => 'impossible to delete deviation : ' . $deviation->getId(),
					'messageStatus' => 'KO',
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/review", name="deviation.review", methods="GET", requirements={"projectID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('DEVIATION_REVIEW_LIST')")
	 */
	public function review(ListGenFactory $lgm, Project $project, Deviation $deviation, TranslatorInterface $translator): Response
	{
		$listReview     = $lgm->getListGen(DeviationReviewList::class);
		$listReviewCrex = $lgm->getListGen(DeviationReviewCrexList::class);
		$editMode       = Deviation::STATUS_DRAFT === $deviation->getStatus() ? 'edit' : null;

		return $this->render('deviation/review/index.html.twig', [
			'project'        => $project,
			'deviation'      => $deviation,
			'editMode'       => $editMode,
			'listReview'     => $listReview->getList($project, $deviation, $translator),
			'listReviewCrex' => $listReviewCrex->getList($project, $deviation, $translator),
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/correction/new", name="deviation.correction.new", requirements={"projectID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_CORRECTION_CREATE', deviation)")
	 */
	public function createCorrection(Request $request, Project $project, Deviation $deviation): Response
	{
		$deviationCorrection = new DeviationCorrection();

		$form = $this->createForm(DeviationCorrectionType::class, $deviationCorrection, [
			'action'           => $this->generateUrl('deviation.correction.new', ['projectID' => $project->getId(), 'deviationID' => $deviation->getId()]),
			'projectID'        => $project->getId(),
			'deviation'        => $deviation,
			'deleteCorrection' => false,
		]);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em                  = $this->getDoctrine()->getManager();
			$deviationCorrection = $form->getData();
			$deviationCorrection->setDeviation($deviation);
			$deviationCorrection->setProject($project);

			$em->persist($deviationCorrection);
			$em->flush();

			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'editMode'    => null,
				'edit'        => 'edit',
			]);
		}

		return $this->render('deviation/correction/create.html.twig', [
			'form'      => $form->createView(),
			'project'   => $project,
			'deviation' => $deviation,
			'editMode'  => null,
			'action'    => 'create',
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/correction/{correctionID}/edit", name="deviation.correction.edit", requirements={"projectID"="\d+", "deviationID"="\d+", "correctionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationCorrection", options={"id"="correctionID"})
	 * @Security("is_granted('DEVIATION_CORRECTION_EDIT', deviationCorrection)")
	 */
	public function editCorrection(Request $request, Project $project, Deviation $deviation, DeviationCorrection $deviationCorrection, DeviationCorrectionHandler $deviationCorrectionHandler): Response
	{
		if ($deviationCorrectionHandler->handle($request, $deviationCorrection, ['projectID' => $project->getId(), 'deviation' => $deviation, 'deleteCorrection' => false])) {
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'editMode'    => null,
				'edit'        => 'edit',
			]);
		}

		return $this->render('deviation/correction/create.html.twig', [
			'form'      => $deviationCorrectionHandler->createView(),
			'project'   => $project,
			'deviation' => $deviation,
			'editMode'  => null,
			'action'    => 'edit',
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/correction/{correctionID}/delete", name="deviation.correction.delete", requirements={"projectID"="\d+", "deviationID"="\d+", "correctionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationCorrection", options={"id"="correctionID"})
	 * @Security("is_granted('DEVIATION_CORRECTION_DELETE', deviationCorrection)")
	 */
	public function deleteCorrection(Request $request, Project $project, RouterInterface $router, Deviation $deviation, DeviationCorrection $deviationCorrection, DeviationCorrectionHandler $deviationCorrectionHandler): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$deviationCorrection->setDeletedAt(new \DateTime());
		$entityManager->persist($deviationCorrection);

		if ($deviationCorrectionHandler->handle($request, $deviationCorrection, ['projectID' => $project->getId(), 'deviation' => $deviation, 'deleteCorrection' => true])) {
			return $this->redirectToRoute('deviation.declaration', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
				'editMode'    => null,
				'edit'        => 'edit',
			]);
		}

		return $this->json([
			'status' => 1,
			'html'   => $this->renderView('deviation/correction/delete.html.twig', [
				'form'   => $deviationCorrectionHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('deviation.correction.delete', [
					'projectID'    => $project->getId(),
					'deviationID'  => $deviation->getId(),
					'correctionID' => $deviationCorrection->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/review/new", name="deviation.review.multiple.new", requirements={"projectID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('DEVIATION_REVIEW_LIST')")
	 */
	public function createReviewMultiple(Request $request, Project $project, DeviationReviewHandler $deviationReviewHandler): Response
	{
		$deviations = $deviationsIds = $json = [];

		if ($json = $request->get('deviations_ids')) {
			$json = json_decode($json, true);
			$json = array_map(function ($item) {
				return $item['id'];
			}, $json);
		}

		if (null !== $request->get('deviations_ids')) {
			foreach ($json as $deviationId) {
				$deviation = $this->getDoctrine()->getRepository(Deviation::class)->findBy(['id' => $deviationId, 'status' => Deviation::STATUS_IN_PROGRESS]);
				if (null != $deviation) {
					$deviations[]    = $deviation;
					$deviationsIds[] = $deviationId;
				}
			}
		}

		$deviationReview = new DeviationReview();
		$form            = $this->createForm(DeviationReviewType::class, $deviationReview, ['project' => $project, 'deleteOrCloseReview' => false, 'isCrex' => false]);

		$form->get('deviationIds')->setData(implode(',', $deviationsIds));

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$ids = $form->get('deviationIds')->getData();

			foreach (explode(',', $ids) as $deviationId) {
				$deviationReview = new DeviationReview();
				$deviation       = $em->getRepository(Deviation::class)->findOneBy(['id' => $deviationId, 'status' => Deviation::STATUS_IN_PROGRESS]);
				$deviationReview->setType($form->get('type')->getData());
				$deviationReview->setReader($form->get('reader')->getData());
				$deviationReview->setComment($form->get('comment')->getData());
				$deviationReview->setDoneAt($form->get('doneAt')->getData());
				// Deviation courante
				$deviationReview->setDeviation($deviation);
				// Revue normale
				$deviationReview->setIsCrex(0);
				// STATUS_EDITION = 2 = En rédaction
				$deviationReview->setStatus($deviationReview::STATUS_EDITION);

				// Numero de la revue
				$deviationReviews = $em->getRepository(DeviationReview::class)->findBy(['deviation' => $deviation, 'isCrex' => false]);
				$deviationReview->setNumber(count($deviationReviews) + 1);

				$em->persist($deviationReview);
			}

			$em->flush();

			return $this->redirectToRoute('deviation.list', [
				'projectID' => $project->getId(),
			]);
		}

		return $this->render('deviation/review/create-multiple.html.twig', [
			'form'           => $form->createView(),
			'project'        => $project,
			'deviations'     => $deviations,
			'countDeviation' => [] != $deviations,
			'action'         => 'create',
		]);
	}

	/**
	 * @Route("/deviation/type/{typeID}", name="deviation.get.subType.xhr", methods="GET", requirements={"typeID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationType", options={"id"="typeID"})
	 *
	 * @return Response
	 */
	public function getDeviationSubType(Request $request, DeviationType $deviationType, SerializerInterface $serializer)
	{
		if (!$request->isXmlHttpRequest()) {
			throw new AccessDeniedException();
		}

		try {

			if (!$subTypes = $deviationType->getChildren()) {
				return new JsonResponse(['jobLabel' => '', 'statusLabel' => 'KO', 'errorMessage' => 'The type ' . $deviationType->getCode() . ' does not have subtypes associated'], 500);
			}

			$status       = 200;
			$statusLabel  = 'OK';
			$errorMessage = '';
			$subTypes     = $serializer->serialize($subTypes, 'json', ['groups' => ['type']]);

		} catch (Exception $exception) {

			$status       = 500;
			$statusLabel  = 'KO';
			$subTypes     = '';
			$errorMessage = $exception->getMessage();
		}

		return new JsonResponse(['subTypes' => $subTypes, 'statusLabel' => $statusLabel, 'errorMessage' => $errorMessage], $status);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/close", name="deviation.close", requirements={"projectID"="\d+", "deviationID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_DEVIATION_READ')")
	 *
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function closeDeviation(Request $request, ListGenFactory $lgm, Project $project, Deviation $deviation, TranslatorInterface $translator, DeviationService $deviationService)
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {
			return $this->json([
				'messageStatus' => 'OK',
				'html'          => $this->renderView('deviation/modal/closeDeviation.html.twig', []),
			], 200);
		} else {
			$res = $deviationService->closeDeviation($deviation);

			if ($res['isClosed']) {
				$responseStatus = 200;
				$responseData   = [
					'messageError'  => '',
					'messageStatus' => 'OK',
				];
			} else {
				$responseStatus = 200;
				$responseData   = [
					'messageError'       => 'impossible de clôturer la deviation : ' . $deviation->getCode(),
					'messageStatus'      => 'KO',
					'isClosableMessages' => $res['isClosableMessages'],
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/deviation-close//multiple", name="deviation.close.multiple",  methods={"GET", "POST"}, options={"expose"=true})
	 */
	public function closeMultiple(Request $request, DeviationService $deviationService): Response
	{
		$items     = $request->get('items');
		$isConfirm = $request->get('confirm');
		$em        = $this->getDoctrine()->getManager();

		if ('0' === $isConfirm) {
			$deviations = [];
			foreach ($items as $item) {
				$deviations[] = $em->getRepository(Deviation::class)->find($item);
			}

			$deviationsAlreadyClosed = [];
			$deviationsNotClosed     = [];
			$deviationsDraft         = [];

			foreach ($deviations as $deviation) {
				if (Deviation::STATUS_DONE === $deviation->getStatus() && $deviation->getClosedAt()) {
					$deviationsAlreadyClosed[] = $deviation;
				} elseif (Deviation::STATUS_DRAFT === $deviation->getStatus()) {
					$deviationsDraft[] = $deviation;
				} else {
					$deviationsNotClosed[] = $deviation;
				}
			}

			$responseData = [
				'messageStatus' => 'OK',
				'html'          => $this->renderView('deviation/modal/closeDeviations.html.twig', [
					'deviationsNotClosed'     => $deviationsNotClosed,
					'deviationsAlreadyClosed' => $deviationsAlreadyClosed,
					'deviationsDraft'         => $deviationsDraft,
				]),
			];

			$responseStatus = 200;
		} else {
			$res = $deviationService->closeDeviations($items);

			$messageStatus = 'OK';
			foreach ($res as $key => $value) {
				if (!$value['isClosed']) {
					$messageStatus = 'KO';
					break;
				}
			}

			$responseData = [
				'messageStatus'  => $messageStatus,
				'deviationsInfo' => $res,
			];

			$responseStatus = 200;
		}

		return $this->json($responseData, $responseStatus);
	}


	/**
	 * @Route("/{projectID}/deviation/{deviationID}/sample", name="deviation.sample", methods="GET", requirements={"projectID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('ROLE_DEVIATION_READ')")
	 */
	public function sample(Project $project, Deviation $deviation): Response
	{
		$entityManager       = $this->getDoctrine()->getManager();
		$editMode            = Deviation::STATUS_DRAFT === $deviation->getStatus() ? 'edit' : null;
		$deviationAndSamples = $entityManager->getRepository(DeviationAndSample::class)->findBy(['deviation' => $deviation]);


		return $this->render('deviation/sample/associateSample.html.twig', [
			'editMode'            => $editMode,
			'project'             => $project,
			'deviation'           => $deviation,
			'deviationAndSamples' => $deviationAndSamples
		]);
	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/deviationSample/associate", name="deviation.sample.associate", requirements={"projectID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_ASSOCIATE_DEVIATION_SAMPLE', deviation)")
	 */
	public function associateDeviationSample(Request $request, Project $project, Deviation $deviation, DeviationAndSampleHandler $deviationAndSampleHandler): Response
	{
		$options = [
			'deviationID'        => $deviation->getId(),
			'deviationSampleID'  => null,
			'project'            => $project,
			'projects'           => [],
			'deviationSampleIDs' => $this->getDeviationSampleIds($deviation),
			'deviationIDs'       => [],
			'associate'          => true,
			'isSample'           => false,
		];

		$deviationAndSample = new DeviationAndSample();

		if ($deviationAndSampleHandler->handle($request, $deviationAndSample, $options)) {
			return $this->redirectToRoute('deviation.sample', [
				'projectID'   => $project->getId(),
				'deviationID' => $deviation->getId(),
			]);
		}

		return $this->render('deviation/associate_deviation_sample/associate.html.twig', [
			'form'      => $deviationAndSampleHandler->createView(),
			'deviation' => $deviation,
			'project'   => $project,
			'action'    => 'create',
		]);

	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/deviationSample/{deviationSampleID}/dissociate", name="deviation.sample.dissociate", requirements={"projectID"="\d+", "deviationID"="\d+", "deviationSampleID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_ASSOCIATE_DEVIATION_SAMPLE', deviation)")
	 */
	public function disassociateDeviationSample(Project $project, Deviation $deviation, DeviationSample $deviationSample): Response
	{
		return $this->json([
			'html' => $this->renderView('deviation/associate_deviation_sample/dissociate.html.twig', [
				'projectID'         => $project->getId(),
				'deviationID'       => $deviation->getId(),
				'deviationSampleID' => $deviationSample->getId(),
			]),
		]);

	}

	/**
	 * @Route("/{projectID}/deviation/{deviationID}/deviationSample/{deviationSampleID}/dissociate/btn", name="deviation.sample.dissociate.btn", requirements={"projectID"="\d+", "deviationID"="\d+", "deviationSampleID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_ASSOCIATE_DEVIATION_SAMPLE', deviation)")
	 */
	public function disassociateDeviationSampleBtn(Project $project, DeviationSample $deviationSample, Deviation $deviation): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$deviationAndSample = $entityManager->getRepository(DeviationAndSample::class)->findOneBy(['deviation' => $deviation, 'deviationSample' => $deviationSample]);

		$entityManager->remove($deviationAndSample);
		$entityManager->flush();

		return $this->redirectToRoute('deviation.sample', [
			'projectID'   => $project->getId(),
			'deviationID' => $deviation->getId()
		]);
	}

	/**
	 * @throws Exception
	 */
	private function createDeviation(Project $project): Deviation
	{
		$deviation = new Deviation();

		$em          = $this->getDoctrine()->getManager();
		$lastDraftID = $em->getRepository(Deviation::class)->getLastDeviationId(true) + 1;
		$now         = new \DateTime();
		$code        = 'DRAFT-' . $now->format('d-m-Y') . '_' . $lastDraftID;

		$deviation->setCode($code);
		$deviation->setDeclaredBy($this->getUser());
		$deviation->setDeclaredAt(new \DateTime());
		$deviation->setProject($project);
		$deviation->setStatus(Deviation::STATUS_DRAFT);
		$deviation->setIsCrexSubmission(false);

		$em->persist($deviation);
		$em->flush();

		return $deviation;
	}

	/**
	 * @param Deviation $deviation
	 * @return array
	 */
	private function getDeviationSampleIds(Deviation $deviation): array
	{
		$deviationSampleIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$entity = $entityManager->getRepository(DeviationAndSample::class)->findBy(['deviation' => $deviation]);

		foreach ($entity as $object) {
			$deviationSampleIDs[] = $object->getDeviationSample()->getId();
		}

		return $deviationSampleIDs;
	}

	/**
	 * @param DeviationAction $deviationAction
	 * @return array
	 */
	private function getDeviationActionUser(DeviationAction $deviationAction): array
	{
		$userID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$user = $entityManager->getRepository(DeviationAction::class)->find($deviationAction->getId());

		if ($user->getIntervener()) {
			$userID[] = $user->getIntervener()->getId();
		}

		return $userID;
	}

	/**
	 * @param DeviationAction $deviationAction
	 * @return array
	 */
	private function getDeviationActionInterlocutor(DeviationAction $deviationAction): array
	{
		$interlocutorID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$interlocutor = $entityManager->getRepository(DeviationAction::class)->find($deviationAction->getId());

		if ($interlocutor->getInterlocutor()) {
			$interlocutorID[] = $interlocutor->getInterlocutor()->getId();
		}
		return $interlocutorID;
	}

}
