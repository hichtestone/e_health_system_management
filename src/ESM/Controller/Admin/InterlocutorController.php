<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Institution;
use App\ESM\Entity\Interlocutor;
use App\ESM\Form\InterlocutorInstitutionMassType;
use App\ESM\FormHandler\InterlocutorHandler;
use App\ESM\ListGen\Admin\InterlocutorList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InterlocutorController.
 *
 * @Route("/admin/interlocutors")
 */
class InterlocutorController extends AbstractController
{
    private $entityManager;

	/**
	 * @param EntityManagerInterface $entityManager
	 */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin.interlocutors.index")
     * @Security("is_granted('INTERLOCUTOR_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $displayArchived = in_array('ROLE_INTERLOCUTOR_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

        $list = $lgm->getListGen(InterlocutorList::class);

        $institution = $this->entityManager->getRepository(Institution::class)->findAll();

        return $this->render('admin/interlocutor/index.html.twig', [
            'list' => $list->getList($displayArchived, $translator),
            'institution' => $institution,
        ]);
    }

	/**
	 * @Route("/admin/ajax/interlocutors", name="admin.interlocutor.index.ajax")
	 * @Security("is_granted('INTERLOCUTOR_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
        $displayArchived = in_array('ROLE_INTERLOCUTOR_ARCHIVE', $this->getUser()->getRoles()) ? true : false;

        $list = $lgm->getListGen(InterlocutorList::class);
        $list = $list->getList($displayArchived, $translator);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

	/**
	 * @Route("/new", name="admin.interlocutor.new")
	 * @Security("is_granted('INTERLOCUTOR_CREATE')")
	 *
	 * @param Request $request
	 * @param InterlocutorHandler $interlocutorHandler
	 * @return Response
	 */
    public function new(Request $request, InterlocutorHandler $interlocutorHandler): Response
	{
        $interlocutor = new Interlocutor();

        $interlocutor->setCreatedAt(new \DateTime());

        $institution = $this->entityManager->getRepository(Institution::class)->findAll();

        if ($interlocutorHandler->handle($request, $interlocutor)) {
            return $this->redirectToRoute('admin.interlocutors.index');
        }

        return $this->render('admin/interlocutor/create.html.twig', [
            'form' 			=> $interlocutorHandler->createView(),
            'action' 		=> 'create',
            'jobNoRpps' 	=> InterlocutorHandler::jobNoRpps,
            'jobInv' 		=> InterlocutorHandler::jobInv,
            'institution' 	=> $institution,
        ]);
    }

	/**
	 * @Route("/{id}/show", name="admin.interlocutor.show", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('INTERLOCUTOR_SHOW', interlocutor)")
	 *
	 * @param Interlocutor $interlocutor
	 * @param InterlocutorHandler $interlocutorHandler
	 * @return Response
	 */
    public function show(Interlocutor $interlocutor, InterlocutorHandler $interlocutorHandler): Response
	{
        return $this->render('admin/interlocutor/show.html.twig', [
            'interlocutor' 	=> $interlocutor,
            'jobNoRpps' 	=> InterlocutorHandler::jobNoRpps,
            'jobInv' 		=> InterlocutorHandler::jobInv,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.interlocutor.edit", requirements={"id"="\d+"})
     * @Security("is_granted('INTERLOCUTOR_EDIT', interlocutor)")
     *
     * @return RedirectResponse|Response
     */
    public function edit(InterlocutorHandler $interlocutorHandler, Request $request, Interlocutor $interlocutor)
    {
        $institution = $this->entityManager->getRepository(Institution::class)->findAll();

        if ($interlocutorHandler->handle($request, $interlocutor)) {
            return $this->redirectToRoute('admin.interlocutor.show', [
                'id' => $interlocutor->getId(),
            ]);
        }

        return $this->render('admin/interlocutor/create.html.twig', [
            'form' 			=> $interlocutorHandler->createView(),
            'action' 		=> 'edit',
            'jobNoRpps' 	=> InterlocutorHandler::jobNoRpps,
            'jobInv' 		=> InterlocutorHandler::jobInv,
            'institution' 	=> $institution,
        ]);
    }

	/**
	 * @Route("/{id}/archive", name="admin.interlocutor.archive", requirements={"id"="\d+"})
	 * @Security("is_granted('INTERLOCUTOR_ARCHIVE', interlocutor)")
	 *
	 * @param Request $request
	 * @param Interlocutor $interlocutor
	 * @return Response
	 */
    public function archive(Request $request, Interlocutor $interlocutor): Response
	{
        $interlocutor->setDeletedAt(new \DateTime());
        $this->entityManager->persist($interlocutor);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.interlocutor.show', [
            'id' => $request->get('id'),
        ]);
    }

	/**
	 * @Route("/{id}/restore", name="admin.interlocutor.restore", requirements={"id"="\d+"})
	 * @Security("is_granted('INTERLOCUTOR_RESTORE', interlocutor)")
	 *
	 * @param Request $request
	 * @param Interlocutor $interlocutor
	 * @return Response
	 */
    public function restore(Request $request, Interlocutor $interlocutor): Response
	{
        $interlocutor->setDeletedAt(null);
        $this->entityManager->persist($interlocutor);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.interlocutor.show', [
            'id' => $request->get('id'),
        ]);
    }

    /**
     * @Route("/attach-mass/popup", name="admin.interlocutor.attach_mass_popup")
     * @Security("is_granted('INTERLOCUTOR_CREATE')")
     *
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     * @throws TransformationFailedException
     */
    public function attacheMassPopup(Request $request, TranslatorInterface $translator): Response
    {
        $params = json_decode($request->getContent(), true);

        $items = $params['items'] ?? [];

        if (count($items) > 0) {

            $items = array_map(function ($item) {
                return $item['id'];
            }, $items);

            $form = $this->createForm(InterlocutorInstitutionMassType::class, [], [
                'action' => $this->generateUrl('admin.interlocutor.attach_mass'),
            ]);

            $interlocutors = $this->getDoctrine()->getRepository(Interlocutor::class)->findById($items);
            $form->get('interlocutors')->setData($interlocutors);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans('entity.Institution.action.list'),
                    'html' => $this->renderView('admin/interlocutor/attach_interlocutor_mass_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'showConfirmButton' => false,
                ],
            ]);

        } else {

            return $this->json([
                'status' => 0,
                'error' => 'No interlocutor selected',
            ]);
        }
    }

    /**
     * @Route("/attach-mass/", name="admin.interlocutor.attach_mass", methods={"POST"})
     * @Security("is_granted('INTERLOCUTOR_CREATE')")
     */
    public function attacheMass(Request $request): Response
    {
        $form = $this->createForm(InterlocutorInstitutionMassType::class, [], [
            'action' => $this->generateUrl('admin.interlocutor.attach_mass'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $interlocutors = $form->get('interlocutors')->getData();
            $institutions = $form->get('institutions')->getData();

            foreach ($interlocutors as $interlocutor) {
                if (null === $interlocutor->getDeletedAt()) {
                    foreach ($institutions as $institution) {
                        if (null === $institution->getDeletedAt() && !$interlocutor->hasInstitution($institution)) {
                            $interlocutor->addInstitution($institution);
                            $em->persist($interlocutor);
                        }
                    }
                }
            }

            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Success !');

            return $this->redirectToRoute('admin.interlocutors.index');
        }

        return $this->redirectToRoute('admin.interlocutors.index');
    }
}
