<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemAction;
use App\ESM\Entity\DeviationSystemCorrection;
use App\ESM\Entity\DropdownList\DeviationSystem\ProcessSystem;
use App\ESM\Entity\Role;
use App\ESM\Entity\User;
use App\ESM\Form\DeviationSystemCorrectionType;
use App\ESM\Form\DeviationSystemDeclarationType;
use App\ESM\FormHandler\DeviationSystemActionHandler;
use App\ESM\FormHandler\DeviationSystemCorrectionHandler;
use App\ESM\ListGen\DeviationSystemList;
use App\ESM\Service\Deviation\DeviationService;
use App\ESM\Service\ListGen\ListGenFactory;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DeviationSystemController.
 *
 * @Route("/no-conformity/deviation-system")
 */
class DeviationSystemController extends AbstractController
{
	/**
	 * @Route("/", name="deviation.system.list", methods="GET", requirements={"projectID"="\d+"}, options={"expose"=true})
	 */
	public function list(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSystemList::class);

		return $this->render('no_conformity/deviation_system/list.html.twig', [
			'list' => $list->getList($translator),
		]);
	}

	/**
	 * @Route("/ajax", name="deviation.system.list.ajax")
	 */
	public function ListDeviationSystemAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSystemList::class);
		$list = $list->getList($translator);
		$list->setRequestParams($request->query);

		// json response
		return $list->generateResponse();
	}

	/**
	 * @Route("/declaration/{deviationSystemID}/{edit}/{editQA}", name="deviation.system.declaration",
	 *     methods={"GET", "POST"}, defaults={"deviationSystemID"=null, "edit"=null, "editQA"=null}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_LIST')")
	 */
	public function declaration(Request $request, DeviationSystem $deviationSystem = null, int $monitoring = null): Response
	{
		$fromMonitoring 					  = null !== $monitoring;
		$em 								  = $this->getDoctrine()->getManager();
		$editMode 							  = $request->get('edit');
		$editQA								  = $request->get('editQA');
		$isGrantedDeviationSystemQAWrite 	  = $this->isGranted(Role::ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE);
		$listDeviationSystemCorrections 	  = $em->getRepository(DeviationSystemCorrection::class)->findBy(['deviationSystem' => $deviationSystem, 'deletedAt' => null]);
		$deviationSystemActions 			  = $this->getDoctrine()->getRepository(DeviationSystemAction::class)->findBy(['deviationSystem' => $deviationSystem, 'isDone' => true, 'deletedAt' => null]);

		if ($editQA && !$isGrantedDeviationSystemQAWrite) {
			$this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier les données d\'assurance qualité !');
			return $this->redirectToRoute('deviation.system.list');
		}

		if (!$deviationSystem && !$editMode) {
			$editMode = 'edit';
		}

		if (!$deviationSystem) {
			$deviationSystem = $this->createDeviationSystem();
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'edit' 				=> $editMode,
				'editQA'			=> $editQA
			]);
		}

		$declarants = $em->getRepository(User::class)->getUsersByRoles(['ROLE_NO_CONFORMITY_SYSTEM_WRITE']);

		$form = $this->createForm(DeviationSystemDeclarationType::class, $deviationSystem, [
			'action' 		=> $this->generateUrl('deviation.system.declaration', ['deviationSystemID' => $deviationSystem->getId(), 'edit' => 'edit', 'editQA' => $editQA]),
			'declarants' 	=> $declarants,
			'editMode' 		=> $editMode,
			'editQA'		=> $editQA
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$deviationSystem = $form->getData();

			if ($deviationSystem->getStatus() === Deviation::STATUS_DRAFT) {

				$deviationSystem->setCode($this->createCodeDeviationSystem($deviationSystem->getId()));
				$deviationSystem->setStatus(Deviation::STATUS_IN_PROGRESS);
			}

			try {

				$em->persist($deviationSystem);
				$em->flush();

			} catch (Exception $exception) {
				$this->addFlash('danger', 'erreur lors de enregistrement de la deviation !');

				return $this->render('no_conformity/deviation_system/declaration.html.twig', [
					'deviationSystem' 	 				   	=> $deviationSystem,
					'deviationSystemActions' 			   	=> $deviationSystemActions,
					'fromMonitoring' 				 	   	=> $fromMonitoring,
					'form' 			 				 	   	=> $form->createView(),
					'editMode' 		 				 	   	=> $editMode,
					'editQA'							  	=> $editQA,
					'listDeviationSystemCorrections' 	   	=> $listDeviationSystemCorrections,
					'isGrantedDeviationSystemQAWrite'	   	=> $isGrantedDeviationSystemQAWrite
				]);
			}

			$this->addFlash('success', 'enregistrement de la deviation système effectuée !');

			return $this->redirectToRoute('deviation.system.declaration', ['deviationSystemID' => $deviationSystem->getId()]);
		}

		return $this->render('no_conformity/deviation_system/declaration.html.twig', [
			'deviationSystem' 				 	=> $deviationSystem,
			'deviationSystemActions' 		 	=> $deviationSystemActions,
			'editMode' 						 	=> $editMode,
			'editQA'							=> $editQA,
			'form'							 	=> $form->createView(),
			'fromMonitoring' 				 	=> $fromMonitoring,
			'listDeviationSystemCorrections' 	=> $listDeviationSystemCorrections,
			'isGrantedDeviationSystemQAWrite'	=> $isGrantedDeviationSystemQAWrite
		]);
	}

	/**
	 * @Route("/brouillon/save/{deviationSystemID}", name="deviation.system.declaration.brouillon.save", methods="POST", requirements={"deviationSystemID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_DRAFT_CREATE', deviationSystem)")
	 * @throws JsonException
	 */
	public function saveDraft(Request $request, DeviationSystem $deviationSystem): Response
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		$data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

		try {
			$em = $this->getDoctrine()->getManager();

			if (in_array($data['fieldName'], ['ObservedAt', 'OccurenceAt', 'VisaAt'])) {
				$dateTimeField = \DateTime::createFromFormat('d/m/Y', $data['fieldValue']);
				$data['fieldValue'] = $dateTimeField;
			}

			if ($data['fieldName'] === 'DeclaredBy') {
				$declarant = $em->getRepository(User::class)->find($data['fieldValue']);
				$data['fieldValue'] = $declarant;
			}

			if ($data['fieldName'] === 'ReferentQA') {
				$referentQA = $em->getRepository(User::class)->find($data['fieldValue']);
				$data['fieldValue'] = $referentQA;
			}

			if ($data['fieldName'] === 'OfficialQA') {
				$officialQA = $em->getRepository(User::class)->find($data['fieldValue']);
				$data['fieldValue'] = $officialQA;
			}

			if ($data['fieldName'] === 'PotentialImpact') {
				if ('' === $data['fieldValue']) {
					$data['fieldValue'] = null;
				}
			}

			if ($data['fieldName'] === 'Process') {

				$deviationSystem->removeAllProcess();

				foreach ($data['fieldValue'] as $value) {
					$processus = $em->getRepository(ProcessSystem::class)->find($value);
					$data['fieldValue'] = $processus;
					$deviationSystem->addProcess($data['fieldValue']);
				}
			}

			if ('Causality_0' === $data['fieldName'] || 'Causality_1' === $data['fieldName'] || 'Causality_2' === $data['fieldName']) {
				$causality = $deviationSystem->getCausality();
				$causalityID = substr($data['fieldName'], -1);

				if (!in_array($causalityID, $causality, true) && true === $data['fieldValue']) {
					$causality[] = $causalityID;
				} elseif (in_array($causalityID, $causality, true) && false === $data['fieldValue']) {
					unset($causality[array_search($causalityID, $causality, true)]);
				}

				$data['fieldName']  = 'Causality';
				$data['fieldValue'] = $causality;
			}

			if ($data['fieldName'] !== 'Process') {
				$deviationSystem->{'set'.$data['fieldName']}($data['fieldValue']);
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist($deviationSystem);
			$em->flush();

			$responseStatus = 200;
			$responseData = [
				'messageError' => '',
				'messageStatus' => 'OK',
			];

		} catch (Exception $exception) {

			$responseStatus = 200;
			$responseData = [
				'messageError' => 'saving deviation error with '.$data['fieldName'].'and value : '.$data['fieldValue'],
				'messageStatus' => 'KO',
			];
		}

		return new JsonResponse($responseData, $responseStatus);
	}

	/**
	 * @Route("/brouillon/delete/{deviationSystemID}", name="deviation.system.declaration.brouillon.delete", methods="POST", requirements={"deviationSystemID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_DRAFT_DELETE', deviationSystem)")
	 */
	public function deleteDraft(Request $request, DeviationSystem $deviationSystem)
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {

			return $this->json([
				'messageStatus' => 'OK',
				'html' => $this->renderView('deviation/modal/deleteDraft.html.twig', []),
			], 200);

		} else {

			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($deviationSystem);
				$em->flush();

				$responseStatus = 200;
				$responseData = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];

			} catch (Exception $exception) {

				$responseStatus = 200;
				$responseData = [
					'messageError' => 'impossible to delete deviation : '.$deviationSystem->getId(),
					'messageStatus' => 'KO',
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/{deviationSystemID}/close", name="deviation.system.close", requirements={"deviationSystemID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_CLOSE', deviationSystem)")
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function close(Request $request, ListGenFactory $lgm, DeviationSystem $deviationSystem, TranslatorInterface $translator, DeviationService $deviationService)
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {
			return $this->json([
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_system/modal/close.html.twig', []),
			], 200);
		} else {
			$res = $deviationService->closeDeviationSystem($deviationSystem);

			if ($res['isClosed']) {
				$responseStatus = 200;
				$responseData = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];
			} else {
				$responseStatus = 200;
				$responseData = [
					'messageError' => 'impossible de clôturer la deviation : '.$deviationSystem->getCode(),
					'messageStatus' => 'KO',
					'isClosableMessages' => $res['isClosableMessages'],
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/multiple", name="deviation.system.multiple", methods={"GET", "POST"}, options={"expose"=true})
	 *
	 * @param Request $request
	 * @param DeviationService $deviationService
	 * @return Response
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function closeMultiple(Request $request, DeviationService $deviationService): Response
	{
		$items     = $request->get('items');
		$isConfirm = $request->get('confirm');
		$em        = $this->getDoctrine()->getManager();

		if ('0' === $isConfirm) {
			$deviationSystems = [];
			if ($items) {
				foreach ($items as $item) {
					$deviationSystems[] = $em->getRepository(DeviationSystem::class)->find($item);
				}
			}

			$deviationSystemsAlreadyClosed = $deviationSystemsNotClosed = $deviationSystemsDraft = [];

			foreach ($deviationSystems as $deviationSystem) {
				if (Deviation::STATUS_DONE === $deviationSystem->getStatus() && $deviationSystem->getClosedAt()) {
					$deviationSystemsAlreadyClosed[] = $deviationSystem;
				} elseif (Deviation::STATUS_DRAFT === $deviationSystem->getStatus()) {
					$deviationSystemsDraft[] = $deviationSystem;
				} else {
					$deviationSystemsNotClosed[] = $deviationSystem;
				}
			}

			$responseData = [
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_system/modal/closeMulti.html.twig', [
					'deviationSystemsNotClosed' => $deviationSystemsNotClosed,
					'deviationSystemsAlreadyClosed' => $deviationSystemsAlreadyClosed,
					'deviationSystemsDraft' => $deviationSystemsDraft,
				]),
			];

		} else {
			$res = [];
			if ($items) {
				$res = $deviationService->closeDeviationSystems($items);
			}

			$messageStatus = 'OK';
			foreach ($res as $key => $value) {
				if (!$value['isClosed']) {
					$messageStatus = 'KO';
					break;
				}
			}

			$responseData = [
				'messageStatus' => $messageStatus,
				'deviationSystemsInfo' => $res,
			];

		}
		$responseStatus = 200;

		return $this->json($responseData, $responseStatus);
	}

	/**
	 * @Route("/{deviationSystemID}/correction/new", name="deviation.system.correction.new", requirements={"deviationSystemID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 */
	public function createCorrection(Request $request, DeviationSystem $deviationSystem): Response
	{
		$deviationSystemCorrection = new DeviationSystemCorrection();

		$form = $this->createForm(DeviationSystemCorrectionType::class, $deviationSystemCorrection, [
			'action' 			=> $this->generateUrl('deviation.system.correction.new', ['deviationSystemID' => $deviationSystem->getId()]),
			'deviationSystem' 	=> $deviationSystem,
			'deleteCorrection' 	=> false,
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em = $this->getDoctrine()->getManager();
			$deviationSystemCorrection = $form->getData();
			$deviationSystemCorrection->setDeviationSystem($deviationSystem);

			$em->persist($deviationSystemCorrection);
			$em->flush();

			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'editMode' 			=> null,
				'edit' 				=> 'edit',
			]);
		}

		return $this->render('no_conformity/deviation_system/correction/create.html.twig', [
			'form' 				=> $form->createView(),
			'deviationSystem' 	=> $deviationSystem,
			'editMode' 			=> null,
			'action' 			=> 'create',
		]);
	}

	/**
	 * @Route("/{deviationSystemID}/correction/{correctionID}/edit", name="deviation.system.correction.edit", requirements={ "deviationSystemID"="\d+", "correctionID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationCorrection", options={"id"="correctionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_CORRECTION_EDIT', deviationSystemCorrection)")
	 */
	public function editCorrection(Request $request, DeviationSystem $deviationSystem, DeviationSystemCorrection $deviationSystemCorrection, DeviationSystemCorrectionHandler $deviationSystemCorrectionHandler): Response
	{
		if ($deviationSystemCorrectionHandler->handle($request, $deviationSystemCorrection, ['deviationSystem' => $deviationSystem, 'deleteCorrection' => false])) {
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'editMode' 			=> null,
				'edit' 				=> 'edit',
			]);
		}

		return $this->render('no_conformity/deviation_system/correction/create.html.twig', [
			'form' 				=> $deviationSystemCorrectionHandler->createView(),
			'deviationSystem' 	=> $deviationSystem,
			'editMode' 			=> null,
			'action' 			=> 'edit',
		]);
	}

	/**
	 * @Route("/{deviationSystemID}/correction/{correctionID}/delete", name="deviation.system.correction.delete", requirements={"deviationSystemID"="\d+", "correctionID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationCorrection", options={"id"="correctionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_CORRECTION_DELETE', deviationSystemCorrection)")
	 */
	public function deleteCorrection(Request $request, RouterInterface $router, DeviationSystem $deviationSystem, DeviationSystemCorrection $deviationSystemCorrection, DeviationSystemCorrectionHandler $deviationSystemCorrectionHandler): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$deviationSystemCorrection->setDeletedAt(new \DateTime());
		$entityManager->persist($deviationSystemCorrection);

		if ($deviationSystemCorrectionHandler->handle($request, $deviationSystemCorrection, ['deviationSystem' => $deviationSystem, 'deleteCorrection' => true])) {
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'editMode' 			=> null,
				'edit' 				=> 'edit',
			]);
		}

		return $this->json([
			'status' => 1,
			'html' => $this->renderView('no_conformity/deviation_system/correction/delete.html.twig', [
				'form' 		=> $deviationSystemCorrectionHandler->createView(),
				'action' 	=> 'delete',
				'url' 		=> $router->generate('deviation.system.correction.delete', [
					'deviationSystemID' => $deviationSystem->getId(),
					'correctionID' 		=> $deviationSystemCorrection->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/{deviationSystemID}/declaration/action/new", name="deviation.system.declaration.action.new", requirements={"deviationSystemID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_ACTION_CREATE', deviationSystem)")
	 */
	public function createAction(Request $request, DeviationSystem $deviationSystem, DeviationSystemActionHandler $deviationActionHandler, int $monitoring = null): Response
	{
		$deviationSystemAction = new DeviationSystemAction();

		$deviationSystemAction->setDeviationSystem($deviationSystem);
		$deviationSystemAction->setIsCrex(0);
		$deviationSystemAction->setIsDone(1);

		$editMode = Deviation::STATUS_DRAFT === $deviationSystem->getStatus() ? 'edit' : null;

		if ($deviationActionHandler->handle($request, $deviationSystemAction, ['deleteAction' => false])) {
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID'	=> $deviationSystem->getId(),
				'monitoring' 		=> $monitoring,
				'editMode' 			=> $editMode,
			]);
		}

		return $this->render('no_conformity/deviation_system/action/create.html.twig', [
			'form' 			  => $deviationActionHandler->createView(),
			'deviationSystem' => $deviationSystem,
			'action' 		  => 'create',
			'editMode' 		  => $editMode,
		]);
	}

	/**
	 * @Route("/{deviationSystemID}/declaration/action/{actionID}/edit", name="deviation.system.declaration.action.edit", requirements={"deviationSystemID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_ACTION_EDIT', deviationSystem)")
	 */
	public function editAction(Request $request, DeviationSystem $deviationSystem, DeviationSystemActionHandler $deviationSystemActionHandler, DeviationSystemAction $deviationSystemAction, int $monitoring = null): Response
	{
		if ($deviationSystemActionHandler->handle($request, $deviationSystemAction, ['deleteAction' => false])) {
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'monitoring' 		=> $monitoring,
			]);
		}

		return $this->render('no_conformity/deviation_system/action/create.html.twig', [
			'form' 					=> $deviationSystemActionHandler->createView(),
			'deviationSystem' 		=> $deviationSystem,
			'deviationSystemAction' => $deviationSystemAction,
			'action' 				=> 'edit',
			'editMode' 				=> null,
		]);
	}

	/**
	 * @Route("/{deviationSystemID}/declaration/action/{actionID}/delete", name="deviation.system.declaration.action.delete", requirements={"deviationSystemID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_ACTION_DELETE', deviationSystem)")
	 */
	public function deleteAction(Request $request, DeviationSystem $deviationSystem, DeviationSystemAction $deviationSystemAction, RouterInterface $router, DeviationSystemActionHandler $deviationSystemActionHandler, int $monitoring = null): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$deviationSystemAction->setDeletedAt(new \DateTime());
		$entityManager->persist($deviationSystemAction);

		if ($deviationSystemActionHandler->handle($request, $deviationSystemAction, ['deleteAction' => true])) {
			return $this->redirectToRoute('deviation.system.declaration', [
				'deviationSystemID' => $deviationSystem->getId(),
				'monitoring' 		=> $monitoring,
			]);
		}

		return $this->json([
			'status' => 1,
			'html' 	 => $this->renderView('no_conformity/deviation_system/action/delete.html.twig', [
				'form' 	 => $deviationSystemActionHandler->createView(),
				'action' => 'delete',
				'url' 	 => $router->generate('deviation.system.declaration.action.delete', [
					'deviationSystemID' => $deviationSystem->getId(),
					'actionID' => $deviationSystemAction->getId(),
				]),
			]),
		]);
	}

	/**
	 * @return DeviationSystem
	 * @throws Exception
	 */
	private function createDeviationSystem(): DeviationSystem
	{
		$entityManager 	 = $this->getDoctrine()->getManager();
		$deviationSystem = new DeviationSystem();

		$lastDraftID = $entityManager->getRepository(DeviationSystem::class)->getLastDeviationSystemId(true);
		$count 		 = $lastDraftID + 1;
		$now 		 = new \DateTime();
		$code 		 = 'DRAFT-NC-SYSTEM-'.$now->format('d-m-Y').'_'.$count;

		$deviationSystem->setCode($code);
		$deviationSystem->setDeclaredBy($this->getUser());
		$deviationSystem->setDeclaredAt(new \DateTime());
		$deviationSystem->setStatus(Deviation::STATUS_DRAFT);
		$deviationSystem->setIsCrexSubmission(false);

		$entityManager->persist($deviationSystem);
		$entityManager->flush();

		return $deviationSystem;
	}

	/**
	 * @param $deviationSystemID
	 * @return string
	 */
	private function createCodeDeviationSystem($deviationSystemID): string
	{
		return 'NC_' . \date('Y') . '_ESM_S_'. str_pad($deviationSystemID, 3, "0", STR_PAD_LEFT);
	}
}
