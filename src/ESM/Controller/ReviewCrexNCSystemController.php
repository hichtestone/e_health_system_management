<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationReviewAction;
use App\ESM\Entity\DeviationSystemAction;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Entity\DeviationSystemReviewAction;
use App\ESM\FormHandler\DeviationSystemReviewActionHandler;
use App\ESM\FormHandler\DeviationSystemReviewHandler;
use App\ESM\ListGen\DeviationSystemReviewCrexList;
use App\ESM\Repository\DeviationSystemReviewRepository;
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
 * Class ReviewCrexNCSystemController.
 *
 * @Route("/no-conformity")
 */
class ReviewCrexNCSystemController extends AbstractController
{

	/**
	 * @Route("/system-crex", name="no_conformity.system_crex", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_List')")
	 */
	public function index(Request $request, ListGenFactory $lgm, DeviationSystemReviewRepository $deviationSystemReviewRepository, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSystemReviewCrexList::class);

		return $this->render('no_conformity/systemCrex.html.twig', [
			'list' => $list->getList($deviationSystemReviewRepository, $translator),
		]);
	}

	/**
	 * @Route("/system-crex/ajax", name="no_conformity.system_crex.ajax")
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_List')")
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm, DeviationSystemReviewRepository $deviationSystemReviewRepository, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(DeviationSystemReviewCrexList::class);
		$list = $list->getList($deviationSystemReviewRepository, $translator);

		$list->setRequestParams($request->query);

		// json response
		return $list->generateResponse();
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/show", name="no_conformity.system_crex.show", requirements={"reviewSystemCrexID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_SHOW', deviationSystemReview)")
	 */
	public function show(DeviationSystemReview $deviationSystemReview): Response
	{
		$actions                          = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['deviationSystemReview' => $deviationSystemReview]);
		$deviationSystemReviewActionsDone = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['isDone' => true, 'deviationSystemReview' => $deviationSystemReview]);

		return $this->render('no_conformity/review_system_crex/show.html.twig', [
			'deviationSystem' => $deviationSystemReview->getDeviationSystem(),
			'review'          => $deviationSystemReview,
			'actions'         => $actions,
			'reviewActions'   => $deviationSystemReviewActionsDone,
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/edit", name="no_conformity.system_crex.review.edit", requirements={"reviewSystemCrexID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_EDIT', deviationSystemReview)")
	 */
	public function edit(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewHandler $deviationSystemReviewHandler): Response
	{
		// soumis au CREX par :
		$deviationSystemReview->setReader($this->getUser());
		// TYPE CREX
		$deviationSystemReview->setType(DeviationReview::TYPE_CREX);

		if ($deviationSystemReviewHandler->handle($request, $deviationSystemReview, ['deleteOrCloseReview' => false, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.system_crex.show', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->render('no_conformity/review_system_crex/edit.html.twig', [
			'form'            => $deviationSystemReviewHandler->createView(),
			'deviationSystem' => $deviationSystemReview->getDeviationSystem(),
			'review'          => $deviationSystemReview,
			'action'          => 'edit',
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/close", name="no_conformity.system_crex.close", requirements={"reviewSystemCrexID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_CLOSE', deviationSystemReview)")
	 */
	public function close(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewHandler $deviationSystemReviewHandler, RouterInterface $router): Response
	{
		// statut = terminée
		$deviationSystemReview->setStatus(DeviationReview::STATUS_FINISH);
		$deviationSystemReview->setValidatedAt(new \DateTime());


		$entityManager = $this->getDoctrine()->getManager();

		$deviationSystemReviewActions = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['deviationSystemReview' => $deviationSystemReview]);

		foreach ($deviationSystemReviewActions as $deviationSystemReviewAction) {
			$deviationSystemReviewAction->setIsDone(true);

			$deviationSystemAction = new DeviationSystemAction();
			$deviationSystemAction->setDeviationSystem($deviationSystemReviewAction->getDeviationSystemReview()->getDeviationSystem());
			$deviationSystemAction->setTypeAction($deviationSystemReviewAction->getTypeAction());
			$deviationSystemAction->setDescription($deviationSystemReviewAction->getDescription());
			$deviationSystemAction->setApplicationAt($deviationSystemReviewAction->getApplicationAt());
			$deviationSystemAction->setDoneAt($deviationSystemReviewAction->getDoneAt());
			$deviationSystemAction->setStatus($deviationSystemReviewAction->getStatus());
			$deviationSystemAction->setIsDone(true);
			$deviationSystemAction->setIsCrex(true);

			if (null != $deviationSystemReviewAction->getIntervener()) {
				$deviationSystemAction->setIntervener($deviationSystemReviewAction->getIntervener());
				$entityManager->persist($deviationSystemAction);
				$entityManager->persist($deviationSystemReviewAction);
			}
		}
		if ($deviationSystemReviewHandler->handle($request, $deviationSystemReview, ['deleteOrCloseReview' => true, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.system_crex.show', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->json([
			'html' => $this->renderView('no_conformity/review_system_crex/close.html.twig', [
				'form'   => $deviationSystemReviewHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('no_conformity.system_crex.close', [
					'reviewSystemCrexID' => $deviationSystemReview->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/delete", name="no_conformity.system_crex.delete", requirements={"reviewSystemCrexID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_DELETE', deviationSystemReview)")
	 */
	public function delete(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewHandler $deviationSystemReviewHandler, RouterInterface $router): Response
	{
		$deviationSystemReview->setStatus(DeviationReview::STATUS_FINISH);
		$deviationSystemReview->setDeletedAt(new \DateTime());

		if ($deviationSystemReviewHandler->handle($request, $deviationSystemReview, ['deleteOrCloseReview' => true, 'isCrex' => true])) {
			return $this->redirectToRoute('no_conformity.system_crex');
		}

		return $this->json([
			'html' => $this->renderView('no_conformity/review_system_crex/delete.html.twig', [
				'form'   => $deviationSystemReviewHandler->createView(),
				'action' => 'delete',
				'url'    => $router->generate('no_conformity.system_crex.delete', [
					'reviewSystemCrexID' => $deviationSystemReview->getId(),
				]),
			]),
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/action/new", name="no_conformity.system_crex.action.new", requirements={"reviewSystemCrexID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_ACTION_CREATE', deviationSystemReview)")
	 */
	public function newAction(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewActionHandler $deviationSystemReviewActionHandler): Response
	{
		$deviationSystemReviewAction = new DeviationSystemReviewAction();

		$deviationSystemReviewAction->setDeviationSystemReview($deviationSystemReview);
		$deviationSystemReviewAction->setIsDone(false);
		$deviationSystemReviewAction->setIsCrex(true);

		if ($deviationSystemReviewActionHandler->handle($request, $deviationSystemReviewAction, ['deleteReviewAction' => false])) {
			return $this->redirectToRoute('no_conformity.system_crex.show', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->render('no_conformity/review_system_crex/action/create.html.twig', [
			'form'   => $deviationSystemReviewActionHandler->createView(),
			'review' => $deviationSystemReview,
			'action' => 'create',
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/action/{actionID}/edit", name="no_conformity.system_crex.action.edit", requirements={"reviewSystemCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @ParamConverter("deviationSystemReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_ACTION_EDIT', deviationSystemReview)")
	 */
	public function editAction(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewActionHandler $deviationSystemReviewActionHandler, DeviationSystemReviewAction $deviationSystemReviewAction): Response
	{
		if ($deviationSystemReviewActionHandler->handle($request, $deviationSystemReviewAction, ['deleteReviewAction' => false])) {
			return $this->redirectToRoute('no_conformity.system_crex.show', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->render('no_conformity/review_system_crex/action/create.html.twig', [
			'form'   => $deviationSystemReviewActionHandler->createView(),
			'review' => $deviationSystemReview,
			'action' => 'edit',
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/action/{actionID}/delete", name="no_conformity.system_crex.action.delete", requirements={"reviewSystemCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @ParamConverter("deviationSystemReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_ACTION_DELETE', deviationSystemReview)")
	 */
	public function deleteAction(Request $request, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewActionHandler $deviationSystemReviewActionHandler, DeviationSystemReviewAction $deviationSystemReviewAction): Response
	{
		if ($deviationSystemReviewActionHandler->handle($request, $deviationSystemReviewAction, ['deleteReviewAction' => true])) {
			return $this->redirectToRoute('no_conformity.system_crex.show', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->json([
			'html' => $this->renderView('no_conformity/review_system_crex/action/deleteCrex.html.twig', [
				'reviewSystemCrexID' => $deviationSystemReview->getId(),
				'actionID'           => $deviationSystemReviewAction->getId(),
			]),
		]);
	}

	/**
	 * @Route("/system-crex/{reviewSystemCrexID}/action/{actionID}/btn/delete", name="no_conformity.system_crex.action.delete.btn", requirements={"reviewSystemCrexID"="\d+", "actionID"="\d+"})
	 * @ParamConverter("deviationSystemReview", options={"id"="reviewSystemCrexID"})
	 * @ParamConverter("deviationSystemReviewAction", options={"id"="actionID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_ACTION_DELETE', deviationSystemReview)")
	 */
	public function deleteActionBtn(DeviationSystemReview $deviationSystemReview, DeviationSystemReviewAction $deviationSystemReviewAction): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($deviationSystemReviewAction);

		$entityManager->flush();

		return $this->redirectToRoute('no_conformity.system_crex.show', [
			'reviewSystemCrexID' => $deviationSystemReview->getId(),
		]);
	}
}
