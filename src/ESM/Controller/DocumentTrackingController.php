<?php

namespace App\ESM\Controller;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\DocumentTrackingCenter;
use App\ESM\Entity\DocumentTrackingInterlocutor;
use App\ESM\Entity\Project;
use App\ESM\Form\DocumentTrackingCenterMassType;
use App\ESM\Form\DocumentTrackingInterlocutorMassType;
use App\ESM\FormHandler\DocumentTrackingHandler;
use App\ESM\ListGen\DocumentTrackingList;
use App\ESM\Service\ListGen\ListGenFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class DocumentTrackingController extends AbstractController
{
    /**
     * @Route("/{id}/document-tracking", name="project.list.documentTracking", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('DOCUMENTTRACKING_LIST')")
     *
     * @param Request $request
     * @param ListGenFactory $lgm
     * @param Project $project
     * @return Response
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        $list = $lgm->getListGen(DocumentTrackingList::class);

        return $this->render('documentTracking/index.html.twig', [
            'project' => $project,
            'list' => $list->getList($request->getLocale(), $project),
        ]);
    }

    /**
     * @Route("/{id}/ajax/document-tracking", name="project.documentTracking.index.ajax", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('DOCUMENTTRACKING_LIST')")
     *
     * @param Request $request
     * @param ListGenFactory $lgm
     * @param Project $project
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen handle request
        $list = $lgm->getListGen(DocumentTrackingList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

    /**
     * @Route("/{id}/document-tracking/new", name="project.documentTracking.new", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('DOCUMENTTRACKING_CREATE')")
     *
     * @param Request $request
     * @param DocumentTrackingHandler $documentTrackingHandler
     * @param Project $project
     * @return Response
     */
    public function new(Request $request, DocumentTrackingHandler $documentTrackingHandler, Project $project): Response
    {
        $documentTracking = new DocumentTracking();

        $documentTracking->setProject($project);

        if ($documentTrackingHandler->handle($request, $documentTracking, [
            'project' => $project,
            'hasTrackings' => false,
        ])) {
            return $this->redirectToRoute('project.list.documentTracking', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('documentTracking/create.html.twig', [
            'form' => $documentTrackingHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

    /**
     * @Route("/{id}/document-tracking/{idDocumentTracking}/show", name="project.documentTracking.show", requirements={"id"="\d+", "idDocumentTracking":"\d+"})
     * @ParamConverter("documentTracking", options={"id"="idDocumentTracking"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('DOCUMENTTRACKING_SHOW', documentTracking)")
     */
    public function show(Project $project, Request $request, DocumentTracking $documentTracking): Response
    {
        if ($documentTracking->isCenter()) {
            $history = $this->getDoctrine()->getRepository(DocumentTrackingCenter::class)
                ->findBy(['documentTracking' => $documentTracking]);
        }
        if ($documentTracking->isInv()) {
            $history = $this->getDoctrine()->getRepository(DocumentTrackingInterlocutor::class)
                ->findBy(['documentTracking' => $documentTracking]);
        }

        // render
        return $this->render('documentTracking/show.html.twig', [
            'project' => $project,
            'documentTracking' => $documentTracking,
            'history' => $history,
        ]);
    }

    /**
     * @Route("/{id}/document-tracking/{idDocumentTracking}/edit", name="project.documentTracking.edit", requirements={"id"="\d+",  "idDocumentTracking":"\d+"})
     * @ParamConverter("documentTracking", options={"id"="idDocumentTracking"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('DOCUMENTTRACKING_EDIT', documentTracking)")
     *
     * @param Request $request
     * @param Project $project
     * @param DocumentTrackingHandler $documentTrackingHandler
     * @param DocumentTracking $documentTracking
     * @return Response
     */
    public function edit(Request $request, Project $project, DocumentTrackingHandler $documentTrackingHandler, DocumentTracking $documentTracking): Response
    {
        if ($documentTracking->isCenter()) {
            $trackings = $this->getDoctrine()->getRepository(DocumentTrackingCenter::class)
                ->findBy(['documentTracking' => $documentTracking]);
        } else {
            $trackings = $this->getDoctrine()->getRepository(DocumentTrackingInterlocutor::class)
                ->findBy(['documentTracking' => $documentTracking]);
        }

        if ($documentTrackingHandler->handle($request, $documentTracking, [
            'project' => $project,
            'hasTrackings' => count($trackings) > 0,
        ])) {
            return $this->redirectToRoute('project.list.documentTracking', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('documentTracking/create.html.twig', [
            'form' => $documentTrackingHandler->createView(),
            'project' => $project,
            'edit' => true,
        ]);
    }

    /**
     * @Route("/{id}/document-tracking/mass-popup", name="project.documentTracking.mass_popup", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('DOCUMENTTRACKING_LIST')")
     */
    public function massPopup(Request $request, Project $project, TranslatorInterface $translator): JsonResponse
    {
        $params = json_decode($request->getContent(), true);

        $items = $params['items'] ?? [];
        if (count($items) > 0) {
            $items = array_map(function ($item) {
                return $item['id'];
            }, $items);

            // check level sélectionné
            $countryList = [];
            $nbLevel = [0, 0];
            $documentTrackings = $this->getDoctrine()->getRepository(DocumentTracking::class)->findBy(['id' => $items]);

            foreach ($documentTrackings as $documentTracking) {
                ++$nbLevel[$documentTracking->getLevel() - 1];
                if (null !== $documentTracking->getCountry()) {
                    if (!in_array($documentTracking->getCountry()->getId(), $countryList)) {
                        $countryList[] = $documentTracking->getCountry()->getId();
                    }
                }
            }
            if ($nbLevel[0] > 0 && $nbLevel[1] > 0) {
                return $this->json([
                    'status' => 0,
                    'error' => 'Erreur: vous devez sélectionner soit des documents centre, soit des documents interlocuteur',
                ]);
            }
            if (count($countryList) > 1) {
                return $this->json([
                    'status' => 0,
                    'error' => 'Erreur: vous ne devez sélectionner que des documents relié au même pays (ou aucun pays).',
                ]);
            }
            if ($nbLevel[DocumentTracking::levelCenter - 1] > 0) {
                $title = 'Sélection des centres';
                $form = $this->createForm(DocumentTrackingCenterMassType::class, [], [
                    'project' => $project,
                    'country' => $countryList[0] ?? null,
                    'action' => $this->generateUrl('project.documentTracking.mass', ['id' => $project->getId(), 'level' => 'center']),
                ]);
            } else {
                $title = 'Sélection des interlocuteurs';
                $form = $this->createForm(DocumentTrackingInterlocutorMassType::class, [], [
                    'project' => $project,
                    'country' => $countryList[0] ?? null,
                    'action' => $this->generateUrl('project.documentTracking.mass', ['id' => $project->getId(), 'level' => 'interlocutor']),
                ]);
            }
            $form->get('documentTrackings')->setData($documentTrackings);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans($title),
                    'html' => $this->renderView('documentTracking/mass.html.twig', [
                        'form' => $form->createView(),
                        'level' => $documentTrackings[0]->getLevel(),
                    ]),
                    'showConfirmButton' => false,
                ],
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'error' => 'No tracking document selected',
            ]);
        }
    }

    /**
     * @Route("/{id}/document-tracking/{level}/mass", name="project.documentTracking.mass", requirements={"id"="\d+", "level"="interlocutor|center"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('DOCUMENTTRACKING_LIST')")
     */
    public function massSend(Request $request, string $level, Project $project): RedirectResponse
    {
        if ('center' === $level) {
            $form = $this->createForm(DocumentTrackingCenterMassType::class, [], [
                'project' => $project,
                'country' => null,
            ]);
        } else {
            $form = $this->createForm(DocumentTrackingInterlocutorMassType::class, [], [
                'project' => $project,
                'country' => null,
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /**
             * @var DocumentTracking[]
             */
            $docTrackings = $form->get('documentTrackings')->getData();
            /*
             * @var Center[]|Interlocutor[] $entities
             */
            if ('center' === $level) {
                $entities = $form->get('centers')->getData();
            } else {
                $entities = $form->get('interlocutors')->getData();
            }

            foreach ($docTrackings as $docTracking) {
                foreach ($entities as $entity) {
                    // check duplicate
                    if ('center' === $level) {
                        $docTrackingEntity = new DocumentTrackingCenter();
                        $docTrackingEntity->setCenter($entity);
                        $duplicate = $this->getDoctrine()->getRepository(DocumentTrackingCenter::class)
                            ->findOneBy(['center' => $entity, 'documentTracking' => $docTracking]);
                    } else {
                        $docTrackingEntity = new DocumentTrackingInterlocutor();
                        $docTrackingEntity->setInterlocutor($entity);
                        $duplicate = $this->getDoctrine()->getRepository(DocumentTrackingInterlocutor::class)
                            ->findOneBy(['interlocutor' => $entity, 'documentTracking' => $docTracking]);
                    }

                    if (null === $duplicate || null === $duplicate->getSentAt()) {
                        $docTrackingEntity->setDocumentTracking($docTracking);
                        $docTrackingEntity->setSentAt($form->get('sentAt')->getData());

                        $em->persist($docTrackingEntity);
                    }
                }
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Success !');
        }

        return $this->redirectToRoute('project.list.documentTracking', ['id' => $project->getId()]);
    }
}
