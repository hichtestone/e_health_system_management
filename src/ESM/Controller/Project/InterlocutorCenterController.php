<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Center;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\Project;
use App\ESM\Entity\Service;
use App\ESM\FormHandler\InterlocutorCenterHandler;
use App\ESM\FormHandler\InterlocutorHandler;
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
class InterlocutorCenterController extends AbstractController
{
    /**
     * @Route("/{id}/center/{idCenter}/interlocutor-center/new", name="project.interlocutor_center.new", requirements={"id"="\d+", "idCenter"="\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', center)")
     */
    public function new(Request $request, Project $project, Center $center, InterlocutorCenterHandler $formHandler, RouterInterface $router): Response
    {
        $service = new InterlocutorCenter();
        $service->setCenter($center);
        $service->setEnabledAt(new \DateTime());

        if ($formHandler->handle($request, $service, ['center' => $center])) {
            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $center->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('project/interlocutor_center/create.html.twig', [
                'form' 			=> $formHandler->createView(),
                'action' 		=> 'create',
                'url' 			=> $router->generate('project.interlocutor_center.new', ['id' => $project->getId(), 'idCenter' => $center->getId()]),
                'formHelperUrl' => $router->generate('project.interlocutor_center.help', ['id' => $project->getId(), 'idCenter' => $center->getId()]),
            ]),
        ]);
    }

    /**
     * @Route("/{id}/center/{idCenter}/interlocutor-center/help", name="project.interlocutor_center.help", requirements={"id"="\d+", "idCenter"="\d+"})
     * @ParamConverter("center", options={"id"="idCenter"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', center)")
     */
    public function help(Request $request, Project $project, Center $center): Response
    {
        $serviceId = $request->get('service');
        $interlocutorId = $request->get('interlocutor');

        $interlocutors = [];
        if ('' != $serviceId) {
            $service = $this->getDoctrine()->getRepository(Service::class)->find($serviceId);

            // on évite les doublons
            $alreadyAttached = $this->getDoctrine()->getRepository(InterlocutorCenter::class)
                ->findBy(['center' => $center, 'service' => $service]);
            $alreadyAttached = array_map(function ($interlocutorCenter) {
                return $interlocutorCenter->getInterlocutor()->getId();
            }, $alreadyAttached);

            $list = $service->getInstitution()->getInterlocutors();
            foreach ($list as $item) {
                if (!in_array($item->getId(), $alreadyAttached)) {
                    $interlocutors[] = [
                        'value' => $item->getId(),
                        'text' => $item->getFullName(),
                    ];
                }
            }
        }

        $canPI = false;
        if ('' != $interlocutorId) {
            $interlocutor = $this->getDoctrine()->getRepository(Interlocutor::class)->find($interlocutorId);
            $canPI = in_array($interlocutor->getJob()->getId(), InterlocutorHandler::jobInv);
        }

        return $this->json([
            'canPI' => $canPI,
            'interlocutors' => $interlocutors,
        ]);
    }

    /**
     * @Route("/{id}/interlocutor-center/{idInterlocutorCenter}/edit", name="project.interlocutor_center.edit", requirements={"id"="\d+", "idInterlocutorCenter"="\d+"})
     * @ParamConverter("interlocutorCenter", options={"id"="idInterlocutorCenter"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', interlocutorCenter.getCenter())")
     */
    public function edit(Request $request, Project $project, InterlocutorCenter $interlocutorCenter, InterlocutorCenterHandler $formHandler, RouterInterface $router): Response
    {
    	$isPI  = $interlocutorCenter->isPrincipalInvestigator() && (1 === count($interlocutorCenter->getCenter()->getPrincipalInvestigators()));

		if ($formHandler->handle($request, $interlocutorCenter, [
            'center' => $interlocutorCenter->getCenter(),
            'interlocutor' => $interlocutorCenter->getInterlocutor(),
			'isPI' => $isPI,
        ])) {
            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $interlocutorCenter->getCenter()->getId()]);
        }

        if (count($formHandler->getForm()->get('isPrincipalInvestigator')->getErrors())) {
            $msg = $formHandler->getForm()->get('isPrincipalInvestigator')->getErrors()[0]->getMessage();
            $request->getSession()->getFlashBag()->add('danger', $msg);

            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $interlocutorCenter->getCenter()->getId()]);
        }

        if (count($formHandler->getForm()->getErrors())) {
            $msg = 'Cet interlocuteur est déjà rattaché à ce service';
            $request->getSession()->getFlashBag()->add('danger', $msg);

            return $this->redirectToRoute('project.center.show', ['id' => $project->getId(), 'idCenter' => $interlocutorCenter->getCenter()->getId()]);
        }

        return $this->json([
            'status' => 1,
            'html' => $this->renderView('project/interlocutor_center/create.html.twig', [
                'canPI' => in_array($interlocutorCenter->getInterlocutor()->getJob()->getId(), InterlocutorHandler::jobInv),
                'form' => $formHandler->createView(),
                'action' => 'edit',
				'isPI' => $isPI,
                'url' => $router->generate('project.interlocutor_center.edit', ['id' => $project->getId(), 'idInterlocutorCenter' => $interlocutorCenter->getId()]),
            ]),
        ]);
    }

	/**
	 * @Route("/{id}/interlocutor-center/{idInterlocutorCenter}/disable", name="project.interlocutor_center.disable", requirements={"id"="\d+", "idInterlocutorCenter"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("interlocutorCenter", options={"id"="idInterlocutorCenter"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', interlocutorCenter.getCenter())")
	 *
	 * @param Project $project
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return Response
	 */
    public function disable(Project $project, InterlocutorCenter $interlocutorCenter): Response
    {
		return $this->json([
			'html' => $this->renderView('project/interlocutor_center/disable.html.twig', [
				'id' => $project->getId(),
				'idInterlocutorCenter' => $interlocutorCenter->getId(),
			]),
		]);
    }

	/**
	 * @Route("/{id}/interlocutor-center/{idInterlocutorCenter}/disable/btn", name="project.interlocutor_center.disable.btn", requirements={"id"="\d+", "idInterlocutorCenter"="\d+"})
	 * @ParamConverter("interlocutorCenter", options={"id"="idInterlocutorCenter"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', interlocutorCenter.getCenter())")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return Response
	 */
	public function disableBtn(Request $request, Project $project, InterlocutorCenter $interlocutorCenter): Response
	{
		$em = $this->getDoctrine()->getManager();

		// check si dernier PI
		if ($interlocutorCenter->isPrincipalInvestigator() && 1 === count($interlocutorCenter->getCenter()->getPrincipalInvestigators())) {
			$msg = 'Erreur : impossible de supprimer le dernier investigateur principal du centre.';
			$request->getSession()->getFlashBag()->add('danger', $msg);
		} else {
			$interlocutorCenter->setDisabledAt(new \DateTime());
			$em->persist($interlocutorCenter);
			$em->flush();
		}

		return $this->redirectToRoute('project.center.show', [
			'id' => $project->getId(),
			'idCenter' => $interlocutorCenter->getCenter()->getId(),
		]);
	}

	/**
	 * @Route("/{id}/interlocutor-center/{idInterlocutorCenter}/enable", name="project.interlocutor_center.enable", requirements={"id"="\d+", "idInterlocutorCenter"="\d+"})
	 * @ParamConverter("interlocutorCenter", options={"id"="idInterlocutorCenter"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', interlocutorCenter.getCenter())")
	 *
	 * @param Project $project
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return Response
	 */
    public function enable(Project $project, InterlocutorCenter $interlocutorCenter): Response
    {
		return $this->json([
			'html' => $this->renderView('project/interlocutor_center/enable.html.twig', [
				'id' => $project->getId(),
				'idInterlocutorCenter' => $interlocutorCenter->getId(),
			]),
		]);
    }

	/**
	 * @Route("/{id}/interlocutor-center/{idInterlocutorCenter}/enable/btn", name="project.interlocutor_center.enable.btn", requirements={"id"="\d+", "idInterlocutorCenter"="\d+"})
	 * @ParamConverter("interlocutorCenter", options={"id"="idInterlocutorCenter"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CENTER_EDIT', interlocutorCenter.getCenter())")
	 *
	 * @param Project $project
	 * @param InterlocutorCenter $interlocutorCenter
	 * @return Response
	 */
	public function enableBtn(Project $project, InterlocutorCenter $interlocutorCenter): Response
	{
		$em = $this->getDoctrine()->getManager();

		$interlocutorCenter->setDisabledAt(null);
		$em->persist($interlocutorCenter);
		$em->flush();

		return $this->redirectToRoute('project.center.show', [
			'id' => $project->getId(),
			'idCenter' => $interlocutorCenter->getCenter()->getId(),
		]);
	}
}
