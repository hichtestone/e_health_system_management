<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationAndSample;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\DeviationSampleAction;
use App\ESM\Entity\DeviationSampleCorrection;
use App\ESM\Entity\DropdownList\DeviationSample\DecisionTaken;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionCenter;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionContext;
use App\ESM\Entity\DropdownList\DeviationSample\NatureType;
use App\ESM\Entity\DropdownList\DeviationSample\PotentialImpactSample;
use App\ESM\Entity\DropdownList\DeviationSample\ProcessInvolved;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Form\DeviationSampleCorrectionType;
use App\ESM\Form\DeviationSampleDeclarationType;
use App\ESM\FormHandler\DeviationSampleActionHandler;
use App\ESM\FormHandler\DeviationSampleCorrectionHandler;
use App\ESM\FormHandler\DeviationAndSampleHandler;
use App\ESM\ListGen\DeviationSampleList;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationSampleController.
 *
 * @Route("/no-conformity/deviation-sample")
 */
class DeviationSampleController extends AbstractController
{
	/**
	 * @Route("/", name="no_conformity.deviation.sample.index", methods="GET", options={"expose"=true})
	 * @Security("is_granted('DEVIATION_SAMPLE_LIST')")
	 */
	public function index(ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSampleList::class);

		return $this->render('no_conformity/deviation_sample/index.html.twig', [
			'list' => $list->getList($translator),
		]);
	}

	/**
	 * @Route("/ajax", name="no_conformity.deviation.sample.index.ajax", methods="GET")
	 * @Security("is_granted('DEVIATION_SAMPLE_LIST')")
	 */
	public function ListDeviationAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSampleList::class);
		$list = $list->getList($translator);
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/{edit}", name="no_conformity.deviation.sample.declaration",
	 *     methods={"GET", "POST"}, defaults={"deviationSampleID"=null, "edit"=null}, options={"expose"=true})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_LIST')")
	 * @param Request $request
	 * @param DeviationSample|null $deviationSample
	 * @return Response
	 * @throws Exception
	 */
	public function declaration(Request $request, DeviationSample $deviationSample = null): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$user          = $this->getUser();
		$editMode      = $request->get('edit');

		$deviationSampleCorrections = $entityManager->getRepository(DeviationSampleCorrection::class)->findBy(['deviationSample' => $deviationSample, 'deletedAt' => null]);
		$deviationSampleActions     = $entityManager->getRepository(DeviationSampleAction::class)->findBy(['deviationSample' => $deviationSample, 'deletedAt' => null]);
		$deviationAndSamples        = $entityManager->getRepository(DeviationAndSample::class)->findBy(['deviationSample' => $deviationSample]);

		if (!$deviationSample && !$editMode) {
			$editMode = 'edit';
		}

		if (!$deviationSample) {
			$deviationSample = $this->createDeviationSample();
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
				'edit' 				=> $editMode
			]);
		}

		$form = $this->createForm(DeviationSampleDeclarationType::class, $deviationSample, [
			'user' 		 => $user,
			'editMode' 	 => $editMode,
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$deviationSample = $form->getData();
			$year            = \date('Y');

			$deviationSample->setCode('NC_' . $year . '_B_'. str_pad($deviationSample->getId(), 3, "0", STR_PAD_LEFT));

			// change status
			$deviationSample->setStatus(Deviation::STATUS_IN_PROGRESS);

			try {
				if ($deviationSample->getDecisionTaken() !== null) {
					$deviationSample->setDecisionAt(new \DateTime());
				}

				$entityManager->persist($deviationSample);
				$entityManager->flush();

			} catch (Exception $exception) {
				$this->addFlash('danger', 'erreur lors de l\'enregistrement de la deviation échantillon biologique !');

				return $this->render('no_conformity/deviation_sample/declaration.html.twig', [
					'deviationSample' 				=> $deviationSample,
					'deviationSampleCorrections' 	=> $deviationSampleCorrections,
					'deviationSampleActions' 		=> $deviationSampleActions,
					'deviationAndSamples' 			=> $deviationAndSamples,
					'deviation' 					=> new Deviation(),
					'deviationAction' 				=> new DeviationAction(),
					'deviationCorrection' 			=> new DeviationCorrection(),
					'form' 							=> $form->createView(),
					'editMode' 						=> $editMode,
				]);
			}

			$this->addFlash('success', 'enregistrement de la deviation échantillon biologique effectuée avec success!');

			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId()
			]);
		}

		return $this->render('no_conformity/deviation_sample/declaration.html.twig', [
			'deviationSample' 			 => $deviationSample,
			'deviationSampleCorrections' => $deviationSampleCorrections,
			'deviationSampleActions' 	 => $deviationSampleActions,
			'deviationAndSamples' 		 => $deviationAndSamples,
			'deviation' 				 => new Deviation(),
			'deviationAction' 			 => new DeviationAction(),
			'deviationCorrection' 		 => new DeviationCorrection(),
			'form' 						 => $form->createView(),
			'editMode' 					 => $editMode,
		]);
	}

	/**
	 * @Route("/declaration/brouillon/{deviationSampleID}/save", name="no_conformity.deviation.sample.declaration.brouillon.save",
	 *     defaults={"deviationSampleID"=null, "edit"=null}, options={"expose"=true})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_DRAFT_EDIT', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @return JsonResponse
	 */
	public function saveDraft(Request $request, DeviationSample $deviationSample): JsonResponse
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		$data          = json_decode($request->getContent(), true);
		$entityManager = $this->getDoctrine()->getManager();

		try {
			if (in_array($data['fieldName'], ['ObservedAt', 'OccurrenceAt'])) {
				$dateTimeField      = \DateTime::createFromFormat('d/m/Y', $data['fieldValue']);
				$data['fieldValue'] = $dateTimeField;
			}

			if ('DeclaredBy' === $data['fieldName']) {
				$declarant          = $entityManager->getRepository(User::class)->find($data['fieldValue']);
				$data['fieldValue'] = $declarant;
			}

			if ('DetectionContext' === $data['fieldName']) {
				$detectionContext   = $entityManager->getRepository(DetectionContext::class)->find($data['fieldValue']);
				$data['fieldValue'] = $detectionContext;
			}

			if ('DetectionCenter' === $data['fieldName']) {
				$detectionCenter    = $entityManager->getRepository(DetectionCenter::class)->find($data['fieldValue']);
				$data['fieldValue'] = $detectionCenter;
			}

			if ('NatureType' === $data['fieldName']) {
				$natureType         = $entityManager->getRepository(NatureType::class)->find($data['fieldValue']);
				$data['fieldValue'] = $natureType;
			}

			if ('PotentialImpactSample' === $data['fieldName']) {
				$potentialImpactSample    = $entityManager->getRepository(PotentialImpactSample::class)->find($data['fieldValue']);
				$data['fieldValue'] = $potentialImpactSample;
			}

			if ('Grade' === $data['fieldName']) {
				$data['fieldValue'] = (int) $data['fieldValue'];
			}

			if ('DecisionTaken' === $data['fieldName']) {
				if (null !== $data['fieldValue']) {
					$decisionTaken      = $entityManager->getRepository(DecisionTaken::class)->find($data['fieldValue']);
					$data['fieldValue'] = $decisionTaken;
				}
			}

			$currentProcessInvolves = $deviationSample->getProcessInvolves()->toArray();
			$projectIds = array_map(function($arr) { return $arr->getId(); }, $currentProcessInvolves);
			if ('ProcessInvolves' === $data['fieldName']) {

				foreach ($projectIds as $id) {
					$processInvolved = $entityManager->getRepository(ProcessInvolved::class)->findOneBy(['id' => $id]);
					$deviationSample->removeProcessInvolved($processInvolved);
				}
				foreach ($data['fieldValue'] as $value) {
					$processInvolved = $entityManager->getRepository(ProcessInvolved::class)->find($value);
					$deviationSample->addProcessInvolved($processInvolved);
				}
			}

			$currentProjects = $deviationSample->getProjects()->toArray();
			$projectIds = array_map(function($arr) { return $arr->getId(); }, $currentProjects);
			if ('Projects' === $data['fieldName']) {
				foreach ($projectIds as $id) {
					$project = $entityManager->getRepository(Project::class)->findOneBy(['id' => $id]);
					$deviationSample->removeProject($project);
				}
				foreach ($data['fieldValue'] as $value) {
					$project = $entityManager->getRepository(Project::class)->find($value);
					$deviationSample->addProject($project);
				}
			}

			if ('Institutions' === $data['fieldName']) {
				foreach ($data['fieldValue'] as $value) {
					$institution        = $entityManager->getRepository(Institution::class)->find($value);
					$data['fieldValue'] = $institution;
					$deviationSample->addInstitution($data['fieldValue']);
				}
			}

			if ('Causality_0' === $data['fieldName'] || 'Causality_1' === $data['fieldName'] || 'Causality_2' === $data['fieldName']) {
				$causality = $deviationSample->getCausality();
				$causalityID = substr($data['fieldName'], -1);

				if (!in_array($causalityID, $causality, true) && true === $data['fieldValue']) {
					$causality[] = $causalityID;
				} elseif (in_array($causalityID, $causality, true) && false === $data['fieldValue']) {
					unset($causality[array_search($causalityID, $causality, true)]);
				}

				$data['fieldName'] = 'Causality';
				$data['fieldValue'] = $causality;
			}

			if ('ProcessInvolves' !== $data['fieldName'] && 'Projects' !== $data['fieldName'] && 'Institutions' !== $data['fieldName']) {
				$deviationSample->{'set' . $data['fieldName']}($data['fieldValue']);
			}

			$entityManager->persist($deviationSample);
			$entityManager->flush();

			$responseStatus = 200;
			$responseData   = [
				'messageError' => '',
				'messageStatus' => 'OK',
			];
		} catch (Exception $exception) {
			$responseStatus = 200;
			$responseData   = [
				'messageError' => 'saving deviation sample error with ' . $data['fieldName'] . 'and value : ' . $data['fieldValue'],
				'messageStatus' => 'KO',
			];
		}

		return new JsonResponse($responseData, $responseStatus);
	}

	/**
	 * @Route("/declaration/brouillon/{deviationSampleID}/delete", name="no_conformity.deviation.sample.declaration.brouillon.delete",
	 *     defaults={"deviationSampleID"=null, "edit"=null}, options={"expose"=true})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_DRAFT_DELETE', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @return JsonResponse|AccessDeniedException
	 */
	public function deleteDraft(Request $request, DeviationSample $deviationSample)
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
				$em->remove($deviationSample);
				$em->flush();

				$responseStatus = 200;
				$responseData   = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];
			} catch (Exception $exception) {
				$responseStatus = 200;
				$responseData   = [
					'messageError' => 'impossible to delete deviation sample : ' . $deviationSample->getId(),
					'messageStatus' => 'KO',
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/correction/new", name="no_conformity.deviation.sample.declaration.correction.new", requirements={"deviationSampleID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_CORRECTION_CREATE', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @return Response
	 */
	public function newCorrection(Request $request, DeviationSample $deviationSample): Response
	{
		$deviationSampleCorrection = new DeviationSampleCorrection();

		$form = $this->createForm(DeviationSampleCorrectionType::class, $deviationSampleCorrection, [
			'action' => $this->generateUrl('no_conformity.deviation.sample.declaration.correction.new', ['deviationSampleID' => $deviationSample->getId()]),
			'deviationSample' => $deviationSample,
			'deleteCorrectionSample' => false,
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em                        = $this->getDoctrine()->getManager();
			$deviationSampleCorrection = $form->getData();
			$deviationSampleCorrection->setDeviationSample($deviationSample);

			$em->persist($deviationSampleCorrection);
			$em->flush();

			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
				'editMode' => null,
				'edit' => 'edit',
			]);
		}

		return $this->render('no_conformity/deviation_sample/correction/create.html.twig', [
			'form' => $form->createView(),
			'deviationSample' => $deviationSample,
			'editMode' => null,
			'action' => 'create',
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/correction/{deviationSampleCorrectionID}/edit", name="no_conformity.deviation.sample.declaration.correction.edit", requirements={"deviationSampleID"="\d+", "deviationSampleCorrectionID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviationSampleCorrection", options={"id"="deviationSampleCorrectionID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_CORRECTION_EDIT', deviationSampleCorrection)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @param DeviationSampleCorrectionHandler $deviationSampleCorrectionHandler
	 * @return Response
	 */
	public function editCorrection(Request $request, DeviationSample $deviationSample, DeviationSampleCorrection $deviationSampleCorrection, DeviationSampleCorrectionHandler $deviationSampleCorrectionHandler): Response
	{
		if ($deviationSampleCorrectionHandler->handle($request, $deviationSampleCorrection, ['deviationSample' => $deviationSample, 'deleteCorrectionSample' => false])) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_sample/correction/create.html.twig', [
			'form' => $deviationSampleCorrectionHandler->createView(),
			'deviationSample' => $deviationSample,
			'editMode' => null,
			'action' => 'edit',
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/correction/{deviationSampleCorrectionID}/delete", name="no_conformity.deviation.sample.declaration.correction.delete", requirements={"deviationSampleID"="\d+", "deviationSampleCorrectionID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviationSampleCorrection", options={"id"="deviationSampleCorrectionID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_CORRECTION_EDIT', deviationSampleCorrection)")
	 * @param Request $request
	 * @param RouterInterface $router
	 * @param DeviationSample $deviationSample
	 * @param DeviationSampleCorrection $deviationSampleCorrection
	 * @param DeviationSampleCorrectionHandler $deviationSampleCorrectionHandler
	 * @return Response
	 */
	public function deleteCorrection(Request $request, RouterInterface $router, DeviationSample $deviationSample, DeviationSampleCorrection $deviationSampleCorrection, DeviationSampleCorrectionHandler $deviationSampleCorrectionHandler): Response
	{
		$deviationSampleCorrection->setDeletedAt(new \DateTime());

		if ($deviationSampleCorrectionHandler->handle($request, $deviationSampleCorrection, ['deviationSample' => $deviationSample, 'deleteCorrectionSample' => true])) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
			]);
		}

		return $this->json([
			'html' => $this->renderView('no_conformity/deviation_sample/correction/delete.html.twig', [
				'form' => $deviationSampleCorrectionHandler->createView(),
				'action' => 'delete',
				'url' => $router->generate('no_conformity.deviation.sample.declaration.correction.delete', [
					'deviationSampleID' => $deviationSample->getId(),
					'deviationSampleCorrectionID' => $deviationSampleCorrection->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/action/new", name="no_conformity.deviation.sample.declaration.action.new",
	 *     requirements={"deviationSampleID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ACTION_CREATE_EDIT', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @param DeviationSampleActionHandler $deviationSampleActionHandler
	 * @return Response
	 */
	public function newAction(Request $request, DeviationSample $deviationSample, DeviationSampleActionHandler $deviationSampleActionHandler): Response
	{
		$deviationSampleAction = new DeviationSampleAction();

		$deviationSampleAction->setDeviationSample($deviationSample);

		$editMode = Deviation::STATUS_DRAFT === $deviationSample->getStatus() ? 'edit' : null;

		$options = [
			'deleteActionSample' => false,
			'projects' => $this->getProjects($deviationSample),
			'intervenants' => [],
			'interlocutors' => [],
			'associateDeviation' => false,
		];

		if ($deviationSampleActionHandler->handle($request, $deviationSampleAction, $options)) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
				'editMode' => $editMode,
			]);
		}

		return $this->render('no_conformity/deviation_sample/action/create.html.twig', [
			'form' => $deviationSampleActionHandler->createView(),
			'deviationSample' => $deviationSample,
			'action' => 'create',
			'editMode' => $editMode,
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/action/{deviationSampleActionID}/edit", name="no_conformity.deviation.sample.declaration.action.edit", requirements={"deviationSampleID"="\d+", "deviationSampleActionID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviationSampleAction", options={"id"="deviationSampleActionID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ACTION_CREATE_EDIT', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @param DeviationSampleActionHandler $deviationSampleActionHandler
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return Response
	 */
	public function editActionDeclaration(Request $request, DeviationSample $deviationSample, DeviationSampleActionHandler $deviationSampleActionHandler, DeviationSampleAction $deviationSampleAction): Response
	{

		$options = [
			'deleteActionSample' => false,
			'projects' => $this->getProjects($deviationSample),
			'intervenants' => $this->getDeviationSampleActionUser($deviationSampleAction),
			'interlocutors' => $this->getDeviationSampleActionInterlocutor($deviationSampleAction),
			'associateDeviation' => false,
		];

		if ($deviationSampleActionHandler->handle($request, $deviationSampleAction, $options)) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_sample/action/create.html.twig', [
			'form' => $deviationSampleActionHandler->createView(),
			'deviationSample' => $deviationSample,
			'deviationSampleAction' => $deviationSampleAction,
			'action' => 'edit',
			'editMode' => null,
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/action/{deviationSampleActionID}/delete",
	 *     name="no_conformity.deviation.sample.declaration.action.delete",
	 *     requirements={"deviationSampleID"="\d+", "deviationSampleActionID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviationSampleAction", options={"id"="deviationSampleActionID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ACTION_DELETE', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @param DeviationSampleAction $deviationSampleAction
	 * @param RouterInterface $router
	 * @param DeviationSampleActionHandler $deviationSampleActionHandler
	 * @return Response
	 */
	public function deleteAction(Request $request, DeviationSample $deviationSample, DeviationSampleAction $deviationSampleAction, RouterInterface $router, DeviationSampleActionHandler $deviationSampleActionHandler): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$deviationSampleAction->setDeletedAt(new \DateTime());
		$entityManager->persist($deviationSampleAction);

		$options = [
			'deleteActionSample' => true,
			'projects' => [],
			'intervenants' => [],
			'interlocutors' => [],
			'associateDeviation' => false,
		];

		if ($deviationSampleActionHandler->handle($request, $deviationSampleAction, $options)) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
			]);
		}

		return $this->json([
			'html' => $this->renderView('no_conformity/deviation_sample/action/delete.html.twig', [
				'form' => $deviationSampleActionHandler->createView(),
				'action' => 'delete',
				'url' => $router->generate('no_conformity.deviation.sample.declaration.action.delete', [
					'deviationSampleID' => $deviationSample->getId(),
					'deviationSampleActionID' => $deviationSampleAction->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/deviation/associate",
	 *     name="no_conformity.deviation.sample.declaration.deviation.associate",
	 *     requirements={"deviationSampleID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ASSOCIATE_DEVIATION', deviationSample)")
	 * @param Request $request
	 * @param DeviationSample $deviationSample
	 * @param DeviationAndSampleHandler $deviationAndSampleHandler
	 * @return Response
	 */
	public function associateDeviation(Request $request, DeviationSample $deviationSample, DeviationAndSampleHandler $deviationAndSampleHandler): Response
	{
		$options = [
			'deviationSampleID'  => $deviationSample->getId(),
			'deviationID' 		 => null,
			'projects' 			 => $this->getProjects($deviationSample),
			'project' 			 => null,
			'deviationIDs' 		 => $this->getDeviationIds($deviationSample),
			'deviationSampleIDs' => [],
			'associate' 		 => true,
			'isSample' 			 => true,
		];

		$deviationAndSample = new DeviationAndSample();

		if ($deviationAndSampleHandler->handle($request, $deviationAndSample, $options)) {
			return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
				'deviationSampleID' => $deviationSample->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_sample/associate_deviation/associate.html.twig', [
			'form' 			  => $deviationAndSampleHandler->createView(),
			'deviationSample' => $deviationSample,
			'action' 		  => 'create',
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/deviation/{deviationID}/disassociate",
	 *     name="no_conformity.deviation.sample.declaration.deviation.disassociate",
	 *     requirements={"deviationSampleID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ASSOCIATE_DEVIATION', deviationSample)")
	 * @param DeviationSample $deviationSample
	 * @param Deviation $deviation
	 * @return Response
	 */
	public function disassociateDeviation(DeviationSample $deviationSample, Deviation $deviation): Response
	{
		return $this->json([
			'html' => $this->renderView('no_conformity/deviation_sample/associate_deviation/dissociate.html.twig', [
				'deviationSampleID' => $deviationSample->getId(),
				'deviationID' => $deviation->getId(),
			]),
		]);
	}

	/**
	 * @Route("/declaration/{deviationSampleID}/deviation/{deviationID}/delete/btn", name="no_conformity.deviation.sample.declaration.deviation.delete.btn", requirements={"deviationSampleID"="\d+", "deviationID"="\d+"})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 * @ParamConverter("deviation", options={"id"="deviationID"})
	 * @Security("is_granted('DEVIATION_SAMPLE_ASSOCIATE_DEVIATION', deviationSample)")
	 * @param DeviationSample $deviationSample
	 * @param Deviation $deviation
	 * @return Response
	 */
	public function disassociateDeviationBtn(DeviationSample $deviationSample, Deviation $deviation): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$deviationAndSample = $entityManager->getRepository(DeviationAndSample::class)->findOneBy(['deviationSample' => $deviationSample, 'deviation' => $deviation]);

		$entityManager->remove($deviationAndSample);
		$entityManager->flush();

		return $this->redirectToRoute('no_conformity.deviation.sample.declaration', [
			'deviationSampleID' => $deviationSample->getId(),
		]);
	}

	/**
	 * @Route("/declaration-close/{deviationSampleID}/close", name="no_conformity.deviation.sample.declaration.close", methods={"GET", "POST"}, requirements={ "deviationSampleID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSample", options={"id"="deviationSampleID"})
	 *
	 * @param Request $request
	 * @param DeviationService $deviationService
	 * @param DeviationSample $deviationSample
	 * @return JsonResponse|AccessDeniedException
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function close(Request $request, DeviationService $deviationService, DeviationSample $deviationSample)
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {
			return $this->json([
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_sample/modal/close.html.twig', []),
			], 200);
		} else {
			$res = $deviationService->closeDeviationSample($deviationSample);

			if ($res['isClosed']) {
				$responseStatus = 200;
				$responseData   = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];
			} else {
				$responseStatus = 200;
				$responseData   = [
					'messageError' => 'impossible de clôturer la deviation : ' . $deviationSample->getCode(),
					'messageStatus' => 'KO',
					'isClosableMessages' => $res['isClosableMessages'],
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/declaration-close/multiple", name="no_conformity.deviation.sample.declaration.multiple", methods={"GET", "POST"}, options={"expose"=true})
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
			$deviationSamples = [];
			if ($items) {
				foreach ($items as $item) {
					$deviationSamples[] = $em->getRepository(DeviationSample::class)->find($item);
				}
			}

			$deviationSamplesAlreadyClosed = $deviationSamplesNotClosed = $deviationSamplesDraft = [];

			foreach ($deviationSamples as $deviationSample) {
				if (Deviation::STATUS_DONE === $deviationSample->getStatus() && $deviationSample->getClosedAt()) {
					$deviationSamplesAlreadyClosed[] = $deviationSample;
				} elseif (Deviation::STATUS_DRAFT === $deviationSample->getStatus()) {
					$deviationSamplesDraft[] = $deviationSample;
				} else {
					$deviationSamplesNotClosed[] = $deviationSample;
				}
			}

			$responseData = [
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_sample/modal/closeMulti.html.twig', [
					'deviationSamplesNotClosed' => $deviationSamplesNotClosed,
					'deviationSamplesAlreadyClosed' => $deviationSamplesAlreadyClosed,
					'deviationSamplesDraft' => $deviationSamplesDraft,
				]),
			];

		} else {
			$res = [];
			if ($items) {
				$res = $deviationService->closeDeviationSamples($items);
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
				'deviationSamplesInfo' => $res,
			];

		}
		$responseStatus = 200;

		return $this->json($responseData, $responseStatus);
	}

	/**
	 * @return DeviationSample
	 * @throws Exception
	 */
	private function createDeviationSample(): DeviationSample
	{
		$entityManager   = $this->getDoctrine()->getManager();
		$deviationSample = new DeviationSample();

		$lastDraftID = $entityManager->getRepository(DeviationSample::class)->getLastDeviationSampleId(true);
		$count       = $lastDraftID + 1;
		$now         = new \DateTime();
		$code        = 'DRAFT-SAMPLE-' . $now->format('d-m-Y') . '_' . $count;

		$deviationSample->setCode($code);
		$deviationSample->setCreatedBy($this->getUser());
		$deviationSample->setDeclaredBy($this->getUser());
		$deviationSample->setDeclaredAt(new \DateTime());
		$deviationSample->setStatus(Deviation::STATUS_DRAFT);

		$entityManager->persist($deviationSample);
		$entityManager->flush();

		return $deviationSample;
	}


	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return array
	 */
	private function getDeviationSampleActionUser(DeviationSampleAction $deviationSampleAction): array
	{
		$userID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$user = $entityManager->getRepository(DeviationSampleAction::class)->find($deviationSampleAction->getId());

		if ($user->getUser()) {
			$userID[] = $user->getUser()->getId();
		}

		return $userID;
	}

	/**
	 * @param DeviationSampleAction $deviationSampleAction
	 * @return array
	 */
	private function getDeviationSampleActionInterlocutor(DeviationSampleAction $deviationSampleAction): array
	{
		$interlocutorID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$interlocutor = $entityManager->getRepository(DeviationSampleAction::class)->find($deviationSampleAction->getId());

		if ($interlocutor->getInterlocutor()) {
			$interlocutorID[] = $interlocutor->getInterlocutor()->getId();
		}
		return $interlocutorID;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return array
	 */
	private function getProjects(DeviationSample $deviationSample): array
	{
		$projects = [];

		foreach ($deviationSample->getProjects()->toArray() as $project) {
			$projects[] = $project->getId();
		}

		return $projects;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return array
	 */
	private function getDeviationIds(DeviationSample $deviationSample): array
	{
		$deviationIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$entity = $entityManager->getRepository(DeviationAndSample::class)->findBy(['deviationSample' => $deviationSample]);

		foreach ($entity as $object) {
			$deviationIDs[] = $object->getDeviation()->getId();
		}

		return $deviationIDs;
	}
}
