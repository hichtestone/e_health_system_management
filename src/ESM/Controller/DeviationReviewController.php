<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationReviewAction;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\DeviationReviewActionHandler;
use App\ESM\FormHandler\DeviationReviewHandler;
use App\ESM\ListGen\DeviationReviewCrexList;
use App\ESM\ListGen\DeviationReviewList;
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
 * Class DeviationReviewController.
 *
 * @Route("/projects")
 */
class DeviationReviewController extends AbstractController
{
    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/ajax", name="deviation.review.ajax", methods="GET", requirements={"projectID"="\d+", "deviationID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @Security("is_granted('DEVIATION_REVIEW_LIST')")
     */
    public function indexAjaxReview(Request $request, ListGenFactory $lgm, Project $project, Deviation $deviation, TranslatorInterface $translator): Response
    {
        // listgen handle request
        $listReview = $lgm->getListGen(DeviationReviewList::class);
        $listReview = $listReview->getList($project, $deviation, $translator);
        $listReview->setRequestParams($request->query);

        return $listReview->generateResponse();
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/crex/ajax", name="deviation.review.crex.ajax", methods="GET", requirements={"projectID"="\d+", "deviationID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @Security("is_granted('DEVIATION_REVIEW_LIST')")
     */
    public function indexAjaxReviewCrex(Request $request, ListGenFactory $lgm, Project $project, Deviation $deviation, TranslatorInterface $translator): Response
    {
        // listgen handle request
        $listReviewCrex = $lgm->getListGen(DeviationReviewCrexList::class);
        $listReviewCrex = $listReviewCrex->getList($project, $deviation, $translator);
        $listReviewCrex->setRequestParams($request->query);

        return $listReviewCrex->generateResponse();
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/new", name="deviation.review.new", requirements={"projectID"="\d+","deviationID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @Security("is_granted('DEVIATION_REVIEW_CREATE', deviation)")
     */
    public function new(Request $request, Project $project, Deviation $deviation, DeviationReviewHandler $deviationReviewHandler): Response
    {
        $deviationReview = new DeviationReview();
        // Deviation courante
        $deviationReview->setDeviation($deviation);
        // Revue normale
        $deviationReview->setIsCrex(false);
        // STATUS_EDITION = 2 = En rédaction
        $deviationReview->setStatus($deviationReview::STATUS_EDITION);

        // Numero de la revue
        $deviationReviews = $this->getDoctrine()->getRepository(DeviationReview::class)->findBy(['deviation' => $deviation, 'isCrex' => false]);
        $deviationReview->setNumber(count($deviationReviews) + 1);

        if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $project, 'deleteOrCloseReview' => false, 'isCrex' => false])) {
            return $this->redirectToRoute('deviation.review', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
            ]);
        }

        return $this->render('deviation/review/create.html.twig', [
            'form' => $deviationReviewHandler->createView(),
            'project' => $project,
            'deviation' => $deviation,
            'action' => 'create',
            'editMode' => null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/send/crex", name="deviation.review.send.crex", requirements={"projectID"="\d+","deviationID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @Security("is_granted('DEVIATION_REVIEW_CREATE', deviation)")
     */
    public function sendCrex(Request $request, Project $project, Deviation $deviation): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $deviationReview = new DeviationReview();
        $deviationReview->setReader($this->getUser());
        $deviationReview->setCreatedAt(new \DateTime());
        $deviationReview->setDeviation($deviation);
        $deviationReview->setIsCrex(true);
        $deviationReview->setStatus(DeviationReview::STATUS_EDITION);
        $deviationReview->setType(DeviationReview::TYPE_CREX);

        // Numero d'une revue
        $deviationReviews = $this->getDoctrine()->getRepository(DeviationReview::class)->findBy(['deviation' => $deviation, 'isCrex' => true]);
        $deviationReview->setNumber(count($deviationReviews) + 1);

        // Numero des revues
        $deviationReviews = $this->getDoctrine()->getRepository(DeviationReview::class)->findBy(['isCrex' => true]);
        $deviationReview->setNumberCrex(count($deviationReviews) + 1);

        $entityManager->persist($deviationReview);

        if (false === $deviation->getIsCrexSubmission()) {
			$deviation->setIsCrexSubmission(true);
			$entityManager->persist($deviation);
		}

        $entityManager->flush();

        return $this->redirectToRoute('deviation.review', [
            'projectID'   => $project->getId(),
            'deviationID' => $deviation->getId(),
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/edit", name="deviation.review.edit", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_EDIT', deviationReview)")
     */
    public function edit(Request $request, Project $project, Deviation $deviation, DeviationReviewHandler $deviationReviewHandler, DeviationReview $deviationReview): Response
    {
        if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $project, 'deleteOrCloseReview' => false, 'isCrex' => false])) {
            return $this->redirectToRoute('deviation.review.show', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
                'reviewID' => $deviationReview->getId(),
            ]);
        }

        return $this->render('deviation/review/create.html.twig', [
            'form' 		=> $deviationReviewHandler->createView(),
            'project' 	=> $project,
            'deviation' => $deviation,
            'review' 	=> $deviationReview,
            'action' 	=> 'edit',
            'editMode' 	=> null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/show", name="deviation.review.show", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_SHOW', deviationReview)")
     */
    public function show(Project $project, Deviation $deviation, DeviationReview $deviationReview): Response
    {
        $deviationReviewActionsDone = $this->getDoctrine()->getRepository(DeviationReviewAction::class)->findBy(['isDone' => true, 'deviationReview' => $deviationReview]);
        $actions                    = $this->getDoctrine()->getRepository(DeviationReviewAction::class)->findBy(['deviationReview' => $deviationReview]);
        $deviationActionsDone       = $this->getDoctrine()->getRepository(DeviationAction::class)->findBy(['deviation' => $deviationReview->getDeviation(), 'isDone' => true, 'deletedAt' => null]);

        return $this->render('deviation/review/show.html.twig', [
            'project' 			=> $project,
            'deviation' 		=> $deviation,
            'review' 			=> $deviationReview,
            'reviewActions' 	=> $deviationReviewActionsDone,
            'deviationActions' 	=> $deviationActionsDone,
            'deviationReviewAction' 	=> new DeviationReviewAction(),
            'actions' 			=> $actions,
            'editMode' 			=> null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/reviewCrex/{reviewID}/show", name="deviation.review.crex.show", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_CREX_SHOW', deviationReview)")
     */
    public function showCrex(DeviationReview $deviationReview): Response
    {
        $actions                    = $this->getDoctrine()->getRepository('App:DeviationReviewAction')->findBy(['deviationReview' => $deviationReview]);
        $deviationReviewActionsDone = $this->getDoctrine()->getRepository('App:DeviationReviewAction')->findBy(['isDone' => true, 'deviationReview' => $deviationReview]);

        return $this->render('deviation/review/showCrex.html.twig', [
            'project' 		=> $deviationReview->getDeviation()->getProject(),
            'deviation' 	=> $deviationReview->getDeviation(),
            'review' 		=> $deviationReview,
            'actions' 		=> $actions,
            'reviewActions' => $deviationReviewActionsDone,
            'editMode' 		=> null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/delete", name="deviation.review.delete", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_DELETE', deviationReview)")
     */
    public function delete(Request $request, Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewHandler $deviationReviewHandler, RouterInterface $router): Response
    {
        $deviationReview->setStatus(DeviationReview::STATUS_FINISH);
        $deviationReview->setDeletedAt(new \DateTime());

        if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $project, 'deleteOrCloseReview' => true, 'isCrex' => false])) {
            return $this->redirectToRoute('deviation.review', [
                'projectID' 	=> $project->getId(),
                'deviationID' 	=> $deviation->getId(),
            ]);
        }

        return $this->json([
            'status' => 1,
            'html' 	 => $this->renderView('deviation/review/delete.html.twig', [
                'form' 	 => $deviationReviewHandler->createView(),
                'action' => 'delete',
                'url' 	 => $router->generate('deviation.review.delete', [
                    'projectID'   => $project->getId(),
                    'deviationID' => $deviation->getId(),
                    'reviewID' 	  => $deviationReview->getId(),
                ]),
            ]),
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/close", name="deviation.review.close", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_CLOSE', deviationReview)")
     */
    public function close(Request $request, Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewHandler $deviationReviewHandler, RouterInterface $router): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $deviationReview->setStatus(DeviationReview::STATUS_FINISH);
        $deviationReview->setValidatedAt(new \DateTime());

        $deviationReviewActions = $this->getDoctrine()->getRepository(DeviationReviewAction::class)->findBy(['deviationReview' => $deviationReview]);

        foreach ($deviationReviewActions as $reviewAction) {
            $deviationAction = new DeviationAction();
            $deviationAction->setDeviation($reviewAction->getDeviationReview()->getDeviation());
            $deviationAction->setTypeAction($reviewAction->getTypeAction());
            $deviationAction->setDescription($reviewAction->getDescription());
            $deviationAction->setApplicationAt($reviewAction->getApplicationAt());
            $deviationAction->setDoneAt($reviewAction->getDoneAt());
            $deviationAction->setStatus($reviewAction->getStatus());
            $deviationAction->setIsDone(true);
            $deviationAction->setIsCrex(false);

            if (null !== $reviewAction->getInterlocutor()) {
                $deviationAction->setInterlocutor($reviewAction->getInterlocutor());
                $deviationAction->setTypeManager(DeviationAction::TYPE_MANAGER_CENTER);
                $entityManager->persist($deviationAction);
            }

            if (null !== $reviewAction->getUser()) {
                $deviationAction->setIntervener($reviewAction->getUser());
                $deviationAction->setTypeManager(DeviationAction::TYPE_MANAGER_PROJECT);
                $entityManager->persist($deviationAction);
            }
        }

        if ($deviationReviewHandler->handle($request, $deviationReview, ['project' => $project, 'deleteOrCloseReview' => true, 'isCrex' => false])) {
            return $this->redirectToRoute('deviation.review.show', [
                'projectID' 	=> $project->getId(),
                'deviationID' 	=> $deviation->getId(),
                'reviewID' 		=> $deviationReview->getId(),
            ]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('deviation/review/close.html.twig', [
                'form' 	 => $deviationReviewHandler->createView(),
                'action' => 'delete',
                'url' 	 => $router->generate('deviation.review.close', [
                    'projectID' 	=> $project->getId(),
                    'deviationID' 	=> $deviation->getId(),
                    'reviewID' 		=> $deviationReview->getId(),
                ]),
            ]),
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/show/action/new", name="deviation.review.show.action.new", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @Security("is_granted('DEVIATION_REVIEW_ACTION_CREATE', deviationReview)")
     */
    public function newAction(Request $request, Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler): Response
    {
        $deviationReviewAction = new DeviationReviewAction();

        $deviationReviewAction->setDeviationReview($deviationReview);
        $deviationReviewAction->setIsDone(false);
        $deviationReviewAction->setIsCrex(false);

        $options = [
        	'project' => $project,
			'deleteReviewAction' => false,
			'user' => [],
			'interlocutor' => [],
		];

        if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
            return $this->redirectToRoute('deviation.review.show', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
                'reviewID' => $deviationReview->getId(),
            ]);
        }

        return $this->render('deviation/review/action/create.html.twig', [
            'form' => $deviationReviewActionHandler->createView(),
            'project' => $project,
            'deviation' => $deviation,
            'review' => $deviationReview,
            'action' => 'create',
            'editMode' => null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/show/action/{actionID}/edit", name="deviation.review.show.action.edit", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+", "actionID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
     * @Security("is_granted('DEVIATION_REVIEW_ACTION_EDIT', deviationReview)")
     */
    public function editAction(Request $request, Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewActionHandler $deviationReviewActionHandler, DeviationReviewAction $deviationReviewAction): Response
    {
		$options = [
			'project' => $project,
			'deleteReviewAction' => false,
			'user' => $this->getDeviationReviewActionUser($deviationReviewAction),
			'interlocutor' => $this->getDeviationReviewActionInterlocutor($deviationReviewAction),
		];

        if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
            return $this->redirectToRoute('deviation.review.show', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
                'reviewID' => $deviationReview->getId(),
            ]);
        }

        return $this->render('deviation/review/action/create.html.twig', [
            'form' => $deviationReviewActionHandler->createView(),
            'project' => $project,
            'deviation' => $deviation,
            'review' => $deviationReview,
            'action' => 'edit',
            'editMode' => null,
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/show/action/{actionID}/delete", name="deviation.review.show.action.delete", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+", "actionID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
     * @Security("is_granted('DEVIATION_REVIEW_ACTION_DELETE', deviationReview)")
     */
    public function deleteAction(Request $request, Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewAction $deviationReviewAction, DeviationReviewActionHandler $deviationReviewActionHandler, RouterInterface $router): Response
    {
		$options = [
			'project' => $project,
			'deleteReviewAction' => true,
			'user' => [],
			'interlocutor' => [],
		];

        if ($deviationReviewActionHandler->handle($request, $deviationReviewAction, $options)) {
            return $this->redirectToRoute('deviation.review.show', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
                'reviewID' => $deviationReview->getId(),
            ]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('deviation/review/action/delete.html.twig', [
                'projectID' => $project->getId(),
                'deviationID' => $deviation->getId(),
                'reviewID' => $deviationReview->getId(),
                'actionID' => $deviationReviewAction->getId(),
            ]),
        ]);
    }

    /**
     * @Route("/{projectID}/deviation/{deviationID}/review/{reviewID}/show/action/{actionID}/delete/btn", name="deviation.review.show.action.delete.btn", requirements={"projectID"="\d+", "deviationID"="\d+", "reviewID"="\d+", "actionID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("deviation", options={"id"="deviationID"})
     * @ParamConverter("deviationReview", options={"id"="reviewID"})
     * @ParamConverter("deviationReviewAction", options={"id"="actionID"})
     * @Security("is_granted('DEVIATION_REVIEW_ACTION_DELETE', deviationReview)")
     */
    public function deleteActionBtn(Project $project, Deviation $deviation, DeviationReview $deviationReview, DeviationReviewAction $deviationReviewAction): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($deviationReviewAction);
        $entityManager->flush();

        return $this->redirectToRoute('deviation.review.show', [
            'projectID'   => $project->getId(),
            'deviationID' => $deviation->getId(),
            'reviewID' 	  => $deviationReview->getId(),
			'actionID' 	  => $deviationReviewAction->getId(),
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
