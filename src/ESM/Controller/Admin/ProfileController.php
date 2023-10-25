<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Profile;
use App\ESM\Entity\Role;
use App\ESM\FormHandler\ProfileHandler;
use App\ESM\ListGen\Admin\ProfileList;
use App\ESM\Repository\ProfileRepository;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController.
 *
 * @Route("/admin/profiles")
 */
class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin.profiles.index")
     * @Security("is_granted('PROFILE_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm): Response
    {
        $list = $lgm->getListGen(ProfileList::class);

        return $this->render('admin/profile/index.html.twig', [
            'list' => $list->getList($request->getLocale()),
        ]);
    }

	/**
	 * @Route("/admin/ajax/profiles", name="admin.profile.index.ajax")
	 * @Security("is_granted('PROFILE_LIST')")
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm): Response
	{
        $list = $lgm->getListGen(ProfileList::class);
        $list = $list->getList($request->getLocale());
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

	/**
	 * @Route("/new", name="admin.profile.new")
	 * @Security("is_granted('PROFILE_CREATE')")
	 * @param Request $request
	 * @param ProfileHandler $profileHandler
	 * @return Response
	 */
    public function new(Request $request, ProfileHandler $profileHandler): Response
	{
        $profile = new Profile();

        if ($profileHandler->handle($request, $profile)) {
            return $this->redirectToRoute('admin.profiles.index');
        }

        return $this->render('admin/profile/create.html.twig', [
            'action' => 'create',
            'form' => $profileHandler->createView(),
        ]);
    }

	/**
	 * @Route("/{id}/show", name="admin.profile.show", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('PROFILE_SHOW', profile)")
	 * @param Profile $profile
	 * @return Response
	 */
    public function show(Profile $profile): Response
	{
        return $this->render('admin/profile/show.html.twig', [
            'profile' => $profile,
            'action' => 'show',
            'roles' => [
                'choices' => $this->getDoctrine()->getRepository(Role::class)->findBy(['parent' => null], ['position' => 'ASC']),
                'values' => array_map(function ($role) {
                    return $role->getId();
                }, $profile->getRoles()->toArray()),
            ],
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.profile.edit", requirements={"id"="\d+"})
     * @Security("is_granted('PROFILE_EDIT', profile)")
     *
     * @return RedirectResponse|Response
     */
    public function edit(ProfileHandler $profileHandler, Request $request, Profile $profile)
    {
        $options = ['action' => $this->generateUrl('admin.profile.edit', ['id' => $profile->getId()])];

        if ($profileHandler->handle($request, $profile, $options)) {
            return $this->redirectToRoute('admin.profile.show', ['id' => $profile->getId()]);
        }

        // render
        return $this->render('admin/profile/create.html.twig', [
            'profile' => $profile,
            'form' => $profileHandler->createView(),
            'action' => 'edit',
            'roles' => [
                'choices' => $this->getDoctrine()->getRepository(Role::class)->findBy(['parent' => null], ['position' => 'ASC']),
                'values' => array_map(function ($role) {
                    return $role->getId();
                }, $profile->getRoles()->toArray()),
            ],
        ]);
    }

    /**
     * @Route("/{id}/archive", name="admin.profile.archive", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROFILE_ARCHIVE', profile)")
     *
     * @return Response
     */
    public function archive(Profile $profile, ProfileRepository $profileRepository)
    {
        $profile->setDeletedAt(new \DateTime());
        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.profiles.index', [
            'id' => $profile->getId(),
        ]);
    }

    /**
     * @Route("/{id}/restore", name="admin.profile.restore", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROFILE_RESTORE', profile)")
     *
     * @return Response
     */
    public function restore(Profile $profile, ProfileRepository $profileRepository)
    {
        $profile->setDeletedAt(null);
        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.profiles.index', [
            'id' => $profile->getId(),
        ]);
    }
}
