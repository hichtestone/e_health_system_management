<?php

namespace App\ESM\Controller;

use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationReviewAction;
use App\ESM\FormHandler\DeviationReviewActionHandler;
use App\ESM\FormHandler\DeviationReviewHandler;
use App\ESM\ListGen\DeviationReviewCrexList;
use App\ESM\Repository\DeviationReviewCrexRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class NoConformityController.
 *
 * @Route("/no-conformity")
 */
class NoConformityController extends AbstractController
{
	/**
	 * @Route("/system", name="no_conformity.system", methods="GET", requirements={"id"="\d+"})
	 */
	public function system(): Response
	{
		return $this->render('no_conformity/system.html.twig', []);
	}

	/**
	 * @Route("/protocol-deviation-crex", name="no_conformity.protocol_deviation_crex", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('ROLE_DEVIATION_REVIEW_CREX_READ')")
	 */
	public function deviationCrex(Request $request, ListGenFactory $lgm, DeviationReviewCrexRepository $crexRepository, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationReviewCrexList::class);

		return $this->render('no_conformity/deviation_crex.html.twig', [
			'list' => $list->getListCrexPD($this->getUser(), $request->getLocale(), $crexRepository, $translator),
		]);
	}

	/**
	 * @Route("/ajax/protocol-deviation-crex", name="no_conformity.protocol_deviation_crex.ajax")
	 * @Security("is_granted('ROLE_DEVIATION_REVIEW_CREX_READ')")
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm, DeviationReviewCrexRepository $crexRepository, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationReviewCrexList::class);
		$list = $list->getListCrexPD($this->getUser(), $request->getLocale(), $crexRepository, $translator);
		$list->setRequestParams($request->query);

		// json response
		return $list->generateResponse();
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/show", name="no_conformity.protocol_crex.show", requirements={"reviewCrexID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_SHOW', deviationReview)")
	 */
	public function showCrex(DeviationReview $deviationReview): Response
	{
		$actions                    = $this->getDoctrine()->getRepository('App:DeviationReviewAction')->findBy(['deviationReview' => $deviationReview]);
		$deviationReviewActionsDone = $this->getDoctrine()->getRepository('App:DeviationReviewAction')->findBy(['isDone' => true, 'deviationReview' => $deviationReview]);

		return $this->render('no_conformity/deviation_crex/show.html.twig', [
			'project'       => $deviationReview->getDeviation()->getProject(),
			'deviation'     => $deviationReview->getDeviation(),
			'review'        => $deviationReview,
			'actions'       => $actions,
			'reviewActions' => $deviationReviewActionsDone,
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/edit", name="no_conformity.protocol_crex.edit", requirements={"reviewCrexID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_EDIT', deviationReview)")
	 */
	public function editCrex(Request $request, DeviationReview $deviationReview, DeviationReviewHandler $deviationReviewHandler): Response
	{
		// soumis au CREX par :
		$deviationReview->setReader($this->getUser());
		// TYPE CREX
		$deviationReview->setType($deviationReview::TYPE_CREX);

		if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $deviationReview->getDeviation()->getProject(), 'deleteOrCloseReview' => false, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.protocol_crex.show', [
				'reviewCrexID' => $deviationReview->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_crex/edit.html.twig', [
			'form'      => $deviationReviewHandler->createView(),
			'project'   => $deviationReview->getDeviation()->getProject(),
			'deviation' => $deviationReview->getDeviation(),
			'review'    => $deviationReview,
			'action'    => 'edit',
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/action/new", name="no_conformity.protocol_crex.action.new", requirements={"reviewCrexID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_ACTION_CREATE', deviationReview)")
	 */
	public function newCrexAction(Request $request, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler): Response
	{
		$deviationReviewAction = new DeviationReviewAction();

		$deviationReviewAction->setDeviationReview($deviationReview);
		$deviationReviewAction->setIsDone(false);
		$deviationReviewAction->setIsCrex(true);

		$options = [
			'project' => $deviationReview->getDeviation()->getProject(),
			'deleteReviewAction' => false,
			'user' => [],
			'interlocutor' => [],
		];

		if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
			return $this->redirectToRoute('no_conformity.protocol_crex.show', [
				'reviewCrexID' => $deviationReview->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_crex/action/create.html.twig', [
			'form'   => $deviationReviewActionHandler->createView(),
			'review' => $deviationReview,
			'action' => 'create',
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/action/{actionID}/edit", name="no_conformity.protocol_crex.action.edit", requirements={"reviewCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_ACTION_EDIT', deviationReview)")
	 */
	public function editCrexAction(Request $request, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler, DeviationReviewAction $deviationReviewAction): Response
	{
		$options = [
			'project' => $deviationReview->getDeviation()->getProject(),
			'deleteReviewAction' => false,
			'user' => $this->getDeviationReviewActionUser($deviationReviewAction),
			'interlocutor' => $this->getDeviationReviewActionInterlocutor($deviationReviewAction),
		];

		if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
			return $this->redirectToRoute('no_conformity.protocol_crex.show', [
				'reviewCrexID' => $deviationReview->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_crex/action/create.html.twig', [
			'form'   => $deviationReviewActionHandler->createView(),
			'review' => $deviationReview,
			'action' => 'edit',
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/action/{actionID}/delete", name="no_conformity.protocol_crex.action.delete", requirements={"reviewCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_ACTION_DELETE', deviationReview)")
	 */
	public function deleteCrexAction(Request $request, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler, DeviationReviewAction $deviationReviewAction, RouterInterface $router): Response
	{
		$options = [
			'project' => $deviationReview->getDeviation()->getProject(),
			'deleteReviewAction' => true,
			'user' => [],
			'interlocutor' => [],
		];
		if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
			return $this->redirectToRoute('no_conformity.protocol_crex.show', [
				'reviewCrexID' => $deviationReview->getId(),
			]);
		}

		return $this->json([
			'status' => 1,
			'html'   => $this->renderView('deviation/review/action/deleteCrex.html.twig', [
				'reviewCrexID' => $deviationReview->getId(),
				'actionID'     => $deviationReviewAction->getId(),
			]),
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/action/{actionID}/btn/delete", name="no_conformity.protocol_crex.action.delete.btn", requirements={"reviewCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_ACTION_DELETE', deviationReview)")
	 */
	public function deleteCrexActionBtn(Request $request, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler, DeviationReviewAction $deviationReviewAction, RouterInterface $router): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($deviationReviewAction);

		$entityManager->flush();

		return $this->redirectToRoute('no_conformity.protocol_crex.show', [
			'reviewCrexID' => $deviationReview->getId(),
			'actionID'     => $deviationReviewAction->getId(),
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/close", name="no_conformity.protocol_crex.close", requirements={"reviewCrexID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_CLOSE', deviationReview)")
	 */
	public function closeCrex(Request $request, DeviationReview $deviationReview, DeviationReviewHandler $deviationReviewHandler, RouterInterface $router): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		// statut = terminée
		$deviationReview->setStatus(DeviationReview::STATUS_FINISH);
		$deviationReview->setValidatedAt(new \DateTime());

		$deviationReviewActions = $this->getDoctrine()->getRepository('App:DeviationReviewAction')->findBy(['deviationReview' => $deviationReview]);

		foreach ($deviationReviewActions as $reviewAction) {
			$reviewAction->setIsDone(1);

			$deviationAction = new DeviationAction();
			$deviationAction->setDeviation($reviewAction->getDeviationReview()->getDeviation());
			$deviationAction->setTypeAction($reviewAction->getTypeAction());
			$deviationAction->setDescription($reviewAction->getDescription());
			$deviationAction->setApplicationAt($reviewAction->getApplicationAt());
			$deviationAction->setDoneAt($reviewAction->getDoneAt());
			$deviationAction->setStatus($reviewAction->getStatus());
			$deviationAction->setIsDone(1);
			$deviationAction->setIsCrex(1);

			if (null != $reviewAction->getInterlocutor()) {
				$deviationAction->setInterlocutor($reviewAction->getInterlocutor());
				$deviationAction->setTypeManager($deviationAction::TYPE_MANAGER_CENTER);
				$entityManager->persist($deviationAction);
				$entityManager->persist($reviewAction);
			}
			if (null != $reviewAction->getUser()) {
				$deviationAction->setIntervener($reviewAction->getUser());
				$deviationAction->setTypeManager($deviationAction::TYPE_MANAGER_PROJECT);
				$entityManager->persist($deviationAction);
				$entityManager->persist($reviewAction);
			}
		}

		if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $deviationReview->getDeviation()->getProject(), 'deleteOrCloseReview' => true, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.protocol_crex.show', [
				'reviewCrexID' => $deviationReview->getId(),
			]);
		}

		return $this->json([
			'status' => 1,
			'html'   => $this->renderView('deviation/review/close.html.twig', [
				'form'   => $deviationReviewHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('no_conformity.protocol_crex.close', [
					'reviewCrexID' => $deviationReview->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/protocol-deviation-crex/{reviewCrexID}/delete", name="no_conformity.protocol_crex.delete", requirements={"reviewCrexID"="\d+"})
	 * @ParamConverter("deviationReview", options={"id"="reviewCrexID"})
	 * @Security("is_granted('DEVIATION_REVIEW_CREX_DELETE', deviationReview)")
	 */
	public function deleteCrex(Request $request, DeviationReview $deviationReview, DeviationReviewHandler $deviationReviewHandler, RouterInterface $router): Response
	{
		$deviationReview->setStatus($deviationReview::STATUS_FINISH);
		$deviationReview->setDeletedAt(new \DateTime());

		if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $deviationReview->getDeviation()->getProject(), 'deleteOrCloseReview' => true, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.protocol_deviation_crex');
		}

		return $this->json([
			'status' => 1,
			'html'   => $this->renderView('deviation/review/delete.html.twig', [
				'form'   => $deviationReviewHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('no_conformity.protocol_crex.delete', [
					'reviewCrexID' => $deviationReview->getId(),
				]),
			]),
		]);
	}

	/**
	 * @param DeviationReviewAction $deviationReviewAction
	 * @return array
	 */
	private function getDeviationReviewActionUser(DeviationReviewAction $deviationReviewAction): array
	{
		$userID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$deviationReviewAction = $entityManager->getRepository(DeviationReviewAction::class)->find($deviationReviewAction->getId());

		if ($deviationReviewAction->getUser()) {
			$userID[] = $deviationReviewAction->getUser()->getId();
		}

		return $userID;
	}

	/**
	 * @param DeviationReviewAction $deviationReviewAction
	 * @return array
	 */
	private function getDeviationReviewActionInterlocutor(DeviationReviewAction $deviationReviewAction): array
	{
		$interlocutorID = [];

		$entityManager = $this->getDoctrine()->getManager();

		$deviationReviewAction = $entityManager->getRepository(DeviationReviewAction::class)->find($deviationReviewAction->getId());

		if ($deviationReviewAction->getInterlocutor()) {
			$interlocutorID[] = $deviationReviewAction->getInterlocutor()->getId();
		}

		return $interlocutorID;
	}
}
