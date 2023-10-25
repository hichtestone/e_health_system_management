<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Project;
use App\ESM\Entity\VisitPatient;
use App\ESM\Form\VisitPatientStatusMassType;
use App\ESM\ListGen\VisitPatientList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class VisitPatientController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/visits", name="project.list.visits", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     */
    public function index(ListGenFactory $lgm, Project $project): Response
    {
        $list = $lgm->getListGen(VisitPatientList::class);

        return $this->render('visitPatient/index.html.twig', [
            'list' => $list->getList($project),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/visits/ajax", name="project.visit.index.ajax", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PATIENTTRACKING_LIST')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project)
    {
        // listgen handle request
        $list = $lgm->getListGen(VisitPatientList::class);
        $list = $list->getList($project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

    /**
     * @Route("/{id}/visits/status-mass/popup", name="project.visitPatient.attach_mass_popup", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('ROLE_PATIENTTRACKING_WRITE')")
     *
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     * @throws TransformationFailedException
     */
    public function statusMassPopup(Request $request, TranslatorInterface $translator, Project $project): Response
    {
        $params = json_decode($request->getContent(), true);

        $items = $params['items'] ?? [];
        if (count($items) > 0) {
            $items = array_map(function ($item) {
                return $item['id'];
            }, $items);

            $form = $this->createForm(VisitPatientStatusMassType::class, [], [
                'action' => $this->generateUrl('project.visit.attach_mass', ['id' => $project->getId()]),
            ]);
            $visitPatients = $this->getDoctrine()->getRepository(VisitPatient::class)->findById($items);
            $form->get('visitPatients')->setData($visitPatients);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans('entity.VisitPatient.action.list'),
                    'html' => $this->renderView('visitPatient/status_mass_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'showCloseButton' => true,
                    'showConfirmButton' => false,
                ],
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'error' => 'Aucun patient n\'est séléctionné',
            ]);
        }
    }

    /**
     * @Route("/{id}/visits/status-mass/", name="project.visit.attach_mass", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('ROLE_PATIENTTRACKING_WRITE')")
     *
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    public function statusMass(Request $request, Project $project): Response
    {
        $form = $this->createForm(VisitPatientStatusMassType::class, [], [
            'action' => $this->generateUrl('project.visit.attach_mass', [
                'id' => $project->getId(),
            ]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $visitPatients = $form->get('visitPatients')->getData();
            $status = $form->get('status')->getData();

            foreach ($visitPatients as $visitPatient) {
                $visitPatient->setStatus($status);
                $em->persist($visitPatient);
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Success !');

            return $this->redirectToRoute('project.list.visits', [
                'id' => $project->getId(),
            ]);
        }

        return $this->redirectToRoute('project.list.visits', [
            'id' => $project->getId(),
        ]);
    }
}
