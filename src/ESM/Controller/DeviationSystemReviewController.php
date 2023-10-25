<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemAction;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Entity\DeviationSystemReviewAction;
use App\ESM\FormHandler\DeviationSystemReviewHandler;
use App\ESM\ListGen\DeviationSystemReviewList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\DBAL\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DeviationSystemReviewController.
 *
 * @Route("/no-conformity/deviation-system/{deviationSystemID}/review")
 */
class DeviationSystemReviewController extends AbstractController
{
	/**
	 * @Route("/list", name="deviation.system.review.list", methods="GET", requirements={"deviationSystemID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_LIST', deviationSystem)")
	 */
	public function reviewList(ListGenFactory $lgm, DeviationSystem $deviationSystem, TranslatorInterface $translator): Response
	{
		$listReviews = $lgm->getListGen(DeviationSystemReviewList::class);
		$editMode = Deviation::STATUS_DRAFT === $deviationSystem->getStatus() ? 'edit' : null;

		return $this->render('no_conformity/deviation_system/review/list.html.twig', [
			'deviationSystem' => $deviationSystem,
			'editMode' 	  	  => $editMode,
			'listReviews' 	  => $listReviews->getList($deviationSystem, $translator),
		]);
	}

	/**
	 * @Route("/list/ajax", name="deviation.system.review.list.ajax", methods="GET", requirements={"deviationSystemID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_LIST')")
	 */
	public function ajaxReviewList(Request $request, ListGenFactory $lgm, DeviationSystem $deviationSystem, TranslatorInterface $translator): Response
	{
		$listReview = $lgm->getListGen(DeviationSystemReviewList::class);
		$listReview = $listReview->getList($deviationSystem, $translator);
		$listReview->setRequestParams($request->query);

		return $listReview->generateResponse();
	}

	/**
	 * @Route("/send/crex", name="deviation.system.review.send.crex", requirements={"deviationSystemID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_CREATE', deviationSystem)")
	 */
	public function sendCrex(DeviationSystem $deviationSystem): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$deviationSystemReview = new DeviationSystemReview();
		$deviationSystemReview->setReader($this->getUser());
		$deviationSystemReview->setCreatedAt(new \DateTime());
		$deviationSystemReview->setDeviationSystem($deviationSystem);
		$deviationSystemReview->setIsCrex(true);
		$deviationSystemReview->setStatus(DeviationReview::STATUS_EDITION);
		$deviationSystemReview->setType(DeviationReview::TYPE_CREX);

		// Numero d'une revue
		$deviationSystemReviews = $this->getDoctrine()->getRepository(DeviationSystemReview::class)->findBy(['deviationSystem' => $deviationSystem, 'isCrex' => true]);
		$deviationSystemReview->setNumber(count($deviationSystemReviews) + 1);

		// Numero des revues
		$deviationSystemReviews = $this->getDoctrine()->getRepository(DeviationSystemReview::class)->findBy(['isCrex' => true]);
		$deviationSystemReview->setNumberCrex(count($deviationSystemReviews) + 1);

		$entityManager->persist($deviationSystemReview);

		if (false === $deviationSystem->getIsCrexSubmission()) {
			$deviationSystem->setIsCrexSubmission(true);
			$entityManager->persist($deviationSystem);
		}

		$entityManager->flush();

		return $this->redirectToRoute('deviation.system.review.list', [
			'deviationSystemID' => $deviationSystem->getId(),
		]);
	}

	/**
	 * @Route("/{deviationSystemReviewID}/show", name="deviation.system.review.show", requirements={"deviationSystemID"="\d+", "deviationSystemReviewID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemReview", options={"id"="deviationSystemReviewID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_SHOW', deviationSystemReview)")
	 */
	public function show(DeviationSystem $deviationSystem, DeviationSystemReview $deviationSystemReview): Response
	{
		$deviationSystemReviewActionsDone = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['isDone' => true, 'deviationSystemReview' => $deviationSystemReview]);
		$actions                    	  = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['deviationSystemReview' => $deviationSystemReview]);
		$deviationSystemActionsDone       = $this->getDoctrine()->getRepository(DeviationSystemAction::class)->findBy(['deviationSystem' => $deviationSystemReview->getDeviationSystem(), 'isDone' => true, 'deletedAt' => null]);

		return $this->render('no_conformity/deviation_system/review/show.html.twig', [
			'deviationSystem' 			=> $deviationSystem,
			'deviationSystemReview' 	=> $deviationSystemReview,
			'reviewActions' 			=> $deviationSystemReviewActionsDone,
			'deviationSystemActions' 	=> $deviationSystemActionsDone,
			'actions' 					=> $actions,
			'editMode' 					=> null,
		]);
	}

	/**
	 * @Route("/{deviationSystemReviewID}/edit", name="deviation.system.review.edit", requirements={"deviationSystemID"="\d+", "deviationSystemReviewID"="\d+"})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemReview", options={"id"="deviationSystemReviewID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_EDIT', deviationSystemReview)")
	 */
	public function edit(Request $request, DeviationSystem $deviationSystem, DeviationSystemReviewHandler $deviationSystemReviewHandler, DeviationSystemReview $deviationSystemReview): Response
	{
		if ($deviationSystemReviewHandler->handle($request, $deviationSystemReview, ['deleteOrCloseReview' => false, 'isCrex' => false])) {
			return $this->redirectToRoute('deviation.system.review.show', [
				'deviationSystemID' 	  => $deviationSystem->getId(),
				'deviationSystemReviewID' => $deviationSystemReview->getId(),
			]);
		}

		return $this->render('no_conformity/deviation_system/review/create.html.twig', [
			'form' 				=> $deviationSystemReviewHandler->createView(),
			'deviationSystem' 	=> $deviationSystem,
			'review' 			=> $deviationSystemReview,
			'action' 			=> 'edit',
			'editMode' 			=> null,
		]);
	}

	/**
	 * @Route("/{deviationSystemReviewID}/delete", name="deviation.system.review.delete", requirements={"deviationSystemID"="\d+", "deviationSystemReviewID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemReview", options={"id"="deviationSystemReviewID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_DELETE', deviationSystemReview)")
	 */
	public function delete(Request $request, DeviationSystem $deviationSystem, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewHandler $deviationSystemReviewHandler, RouterInterface $router): Response
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {

			return $this->json([
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_system/review/delete.html.twig', []),
			], 200);

		} else {

			try {
				$em = $this->getDoctrine()->getManager();

				$deviationSystemReview->setStatus(DeviationReview::STATUS_FINISH);
				$deviationSystemReview->setDeletedAt(new \DateTime());

				$em->persist($deviationSystemReview);
				$em->flush();

				$responseStatus = 200;
				$responseData   = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];

			} catch (Exception $exception) {

				$responseStatus = 200;
				$responseData   = [
					'messageError' => 'impossible to delete deviationReview : ' . $deviationSystemReview->getId(),
					'messageStatus' => 'KO',
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}

	/**
	 * @Route("/{deviationSystemReviewID}/close", name="deviation.system.review.close", requirements={"deviationSystemID"="\d+", "deviationSystemReviewID"="\d+"}, options={"expose"=true})
	 * @ParamConverter("deviationSystem", options={"id"="deviationSystemID"})
	 * @ParamConverter("deviationSystemReview", options={"id"="deviationSystemReviewID"})
	 * @Security("is_granted('DEVIATION_SYSTEM_REVIEW_CLOSE', deviationSystemReview)")
	 */
	public function close(Request $request, DeviationSystem $deviationSystem, DeviationSystemReview $deviationSystemReview, DeviationSystemReviewHandler $deviationSystemReviewHandler, RouterInterface $router): Response
	{
		if (!$request->isXmlHttpRequest()) {
			return new AccessDeniedException();
		}

		if ('0' === $request->get('confirm')) {

			return $this->json([
				'messageStatus' => 'OK',
				'html' => $this->renderView('no_conformity/deviation_system/review/close.html.twig', []),
			], 200);

		} else {

			try {

				$entityManager = $this->getDoctrine()->getManager();
				$deviationSystemReview->setStatus(DeviationReview::STATUS_FINISH);
				$deviationSystemReview->setValidatedAt(new \DateTime());

				$deviationSystemReviewActions = $this->getDoctrine()->getRepository(DeviationSystemReviewAction::class)->findBy(['deviationSystemReview' => $deviationSystemReview]);

				foreach ($deviationSystemReviewActions as $deviationSystemReviewAction) {

					$deviationSystemAction = new DeviationSystemAction();
					$deviationSystemAction->setDeviationSystem($deviationSystemReviewAction->getDeviationReview()->getDeviation());
					$deviationSystemAction->setTypeAction($deviationSystemReviewAction->getTypeAction());
					$deviationSystemAction->setDescription($deviationSystemReviewAction->getDescription());
					$deviationSystemAction->setApplicationAt($deviationSystemReviewAction->getApplicationAt());
					$deviationSystemAction->setDoneAt($deviationSystemReviewAction->getDoneAt());
					$deviationSystemAction->setStatus($deviationSystemReviewAction->getStatus());
					$deviationSystemAction->setIsDone(1);
					$deviationSystemAction->setIsCrex(0);

					if (null !== $deviationSystemReviewAction->getInterlocutor()) {

						$deviationSystemAction->setInterlocutor($deviationSystemReviewAction->getInterlocutor());
						$deviationSystemAction->setTypeManager(DeviationAction::TYPE_MANAGER_CENTER);
					}

					if (null !== $deviationSystemReviewAction->getUser()) {

						$deviationSystemAction->setIntervener($deviationSystemReviewAction->getUser());
						$deviationSystemAction->setTypeManager(DeviationAction::TYPE_MANAGER_PROJECT);
					}

					$entityManager->persist($deviationSystemAction);
					$entityManager->flush();
				}

				$entityManager->persist($deviationSystemReview);
				$entityManager->flush();

				$responseStatus = 200;
				$responseData   = [
					'messageError' => '',
					'messageStatus' => 'OK',
				];

			} catch (Exception $exception) {

				$responseStatus = 200;
				$responseData   = [
					'messageError' => 'impossible to close deviationReview : ' . $deviationSystemReview->getId(),
					'messageStatus' => 'KO',
				];
			}

			return $this->json($responseData, $responseStatus);
		}
	}
}
