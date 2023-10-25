<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Project;
use App\ESM\Entity\User;
use App\ESM\Entity\UserProject;
use App\ESM\Form\UserProjectMassType;
use App\ESM\Form\UserType;
use App\ESM\FormHandler\UserHandler;
use App\ESM\ListGen\Admin\UserList;
use App\ESM\Notification\ActivationEsmForETMF;
use App\ESM\Service\ListGen\ListGenFactory;
use App\ESM\Service\ResetPasswordMail\ResetPasswordMail;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserController.
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
    }

	/**
	 * @Route("/", name="admin.user.index")
	 * @Security("is_granted('USER_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function index(Request $request, ListGenFactory $lgm): Response
	{
        // listgen
        $list = $lgm->getListGen(UserList::class);

        // render
        return $this->render('admin/user/index.html.twig', [
            'list' => $list->getList($request->getLocale()),
        ]);
    }

	/**
	 * @Route("/admin/ajax/users", name="admin.user.index.ajax")
	 * @Security("is_granted('USER_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm): Response
	{
        $list = $lgm->getListGen(UserList::class);
        $list = $list->getList($request->getLocale());
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

    /**
     * @Route("/admin/ajax/users/{id}/switch-enabled", name="admin.user.switchEnabled", requirements={"id"="\d+"})
     * @Security("is_granted('USER_ACCESS', userr)")
     *
     * @param User $userr
     * @param Request $request
     * @param ListGenFactory $lgm
     * @param ResetPasswordMail $reset_password_mail
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function switchEnabled(User $userr, Request $request, ListGenFactory $lgm, ResetPasswordMail $reset_password_mail): Response
    {
        $manager = $this->getDoctrine()->getManager();

        // init listgen
        $list = $lgm->getListGen(UserList::class);
        $list = $list->getList();

        // switch enabled
        $userr->setHasAccessEsm(!$userr->getHasAccessESM());

        // envoi mail si activé
        if ($userr->getHasAccessESM()) {
            // generate reset link
            $token = $reset_password_mail->generateToken();
            $userr->setResetPasswordToken($token);
            $userr->setResetPasswordAt(new \DateTime());

            // envoi mail welcome
            $reset_password_mail->sendWelcome($userr);
        }

        // persist
        $manager->persist($userr);
        $manager->flush();

        // préparation json
        $r = [
            'status' => 1,
            'msg' => 'ok',
            'html' => [
                'tr' => $list->renderRowAjax(['u.id' => $userr->getId()]),
                'afterUpdEffect' => $list->getAfterAffectAjax($request->get('actionpos')),
            ],
        ];

        // render
        $response = new Response(json_encode($r));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/new", name="admin.user.new")
     * @Security("is_granted('USER_CREATE')")
     */
    public function new(Request $request, ResetPasswordMail $reset_password_mail, ActivationEsmForETMF $activationEsmForETMF)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['role_access' => $this->isGranted('ROLE_USER_ACCESS')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // envoi mail si activé
            if ($user->getHasAccessESM()) {
                // generate reset link
                $token = $reset_password_mail->generateToken();
                $user->setResetPasswordToken($token);
                $user->setResetPasswordAt(new \DateTime());

                // envoi mail welcome
                $reset_password_mail->sendWelcome($user);
            }
            //ajout de la condition pour l'accès ETMF
            if ($user->getHasAccessEtmf()) {
                // envoi mail d'acivation d'accès
                $activationEsmForETMF->notify($this->getUser(), $request, 'Activation des accès à l\'ETMF ', 'access activation', $user->getEmail(), ['firstname' => $user->getFirstName(), 'lastname' => $user->getLastName()]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin.user.show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'create',
        ]);
    }

	/**
	 * @Route("/show/{id}", name="admin.user.show", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("user", options={"id"="id"})
	 * @Security("is_granted('USER_SHOW', user)")
	 *
	 * @param User $user
	 * @return Response
	 */
    public function show(User $user): Response
	{
        // form
        $form = null;

        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        if ($this->isGranted('USER_EDIT', $user)) {
            $form = $this->createForm(UserType::class, $user, [
                'action' => $this->generateUrl('admin.user.edit', ['id' => $user->getId()]),
                //'form_type' => 'edit',
            ]);
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'projects' => $projects,
            'form' => null !== $form ? $form->createView() : null,
            'action' => 'show',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.user.edit", requirements={"id"="\d+"})
     * @Security("is_granted('USER_EDIT', user)")
     */
    public function edit(User $user, Request $request, UserHandler $userHandler, ResetPasswordMail $reset_password_mail, ActivationEsmForETMF $activationEsmForETMF): Response
    {
        if ($userHandler->handle($request, $user, ['role_access' => $this->isGranted('ROLE_USER_ACCESS')])) {
            $uow = $this->entityManager->getUnitOfWork();
            $uow->computeChangeSets();
            $changeSet = $uow->getEntityChangeSet($user);

            if (isset($changeSet['hasAccessEsm']) &&
                true === $changeSet['hasAccessEsm'][1]
            ) {
                // generate reset link
                $token = $reset_password_mail->generateToken();
                $user->setResetPasswordToken($token);
                $user->setResetPasswordAt(new \DateTime());

                // envoi mail welcome
                $reset_password_mail->sendWelcome($user);
            }
            //ajout de la condition pour l'accès ETMF
            if ($user->getHasAccessEtmf()) {
                // envoi mail d'acivation d'accès
                $activationEsmForETMF->notify($this->getUser(), $request, 'Activation des accès à l\'ETMF ', 'access activation', $user->getEmail(), ['firstname' => $user->getFirstName(), 'lastname' => $user->getLastName()]);
            }

            // goto show
            return $this->redirectToRoute('admin.user.show', ['id' => $user->getId()]);
        }

        // render
        return $this->render('admin/user/create.html.twig', [
            'user' => $user,
            'form' => $userHandler->createView(),
            'action' => 'edit',
        ]);
    }

    /**
     * @Route("/attach-mass/popup", name="admin.user.attach_mass_popup", methods={"POST"})
     * @Security("is_granted('ROLE_USER_WRITE')")
     */
    public function attacheMassPopup(Request $request, TranslatorInterface $translator): Response
    {
        $params = json_decode($request->getContent(), true);
        $items = $params['items'] ?? [];

        if (count($items) > 0) {
            $items = array_map(function ($item) {
                return $item['id'];
            }, $items);

            $form = $this->createForm(UserProjectMassType::class, [], [
                'action' => $this->generateUrl('admin.user.attach_mass'),
            ]);

            $users = $this->getDoctrine()->getRepository(User::class)->findById($items);
            $form->get('users')->setData($users);

            return $this->json([
                'status' => 1,
                'popup' => [
                    'title' => $translator->trans('entity.Project.select'),
                    'html' => $this->renderView('admin/user/attach_project_mass_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'showConfirmButton' => false,
                ],
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'error' => 'No user selected',
            ]);
        }
    }

    /**
     * @Route("/attach-mass/", name="admin.user.attach_mass", methods={"POST"})
     * @Security("is_granted('ROLE_USER_WRITE')")
     */
    public function attacheMass(Request $request): Response
    {
        $form = $this->createForm(UserProjectMassType::class, [], [
            'action' => $this->generateUrl('admin.user.attach_mass'),
        ]);

        $form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
			$users = $form->get('users')->getData();
			$projects = $form->get('projects')->getData();

			foreach ($users as $user) {

                if (null === $user->getDeletedAt()) {

                    $userProjects = $user->getUserProjects();
                    foreach ($projects as $project) {
                        // si déjà dans projet
                        $userProject = null;
                        foreach ($userProjects as $userProject) {
                            if ($userProject->getProject() === $project) {
                                $userProject->setDisabledAt(null);
                                break;
                            }
                        }
                        if (null === $userProject || $userProject->getProject() !== $project) {
                            $userProject = new UserProject();
                            $userProject->setUser($user);
                            $userProject->setProject($project);
                            $userProject->setEnabledAt(new \DateTime());
                        }
                        $em->persist($userProject);
                    }
                }
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Success !');

            return $this->redirectToRoute('admin.user.index');
        }

        return $this->redirectToRoute('admin.user.index');
    }

	/**
	 * @Route("/desactive/user/{id}/project/{idProject}/associate", name="admin.user.project.associate", methods="GET", requirements={"id"="\d+", "idProject"="\d+"})
	 * @ParamConverter("user", options={"id"="id"})
	 * @ParamConverter("project", options={"id"="idProject"})
	 * @Security("is_granted('ROLE_USER_WRITE')")
	 */
	public function associate(User $user, Project $project): Response
	{
		return $this->json([
			'html' => $this->renderView('admin/user/associate.html.twig', [
				'id' => $user->getId(),
				'idProject' => $project->getId(),
			]),
		]);
	}

	/**
	 * @Route("/desactive/user/{id}/project/{idProject}/associate/btn", name="admin.user.project.associate.btn", methods="GET", requirements={"id"="\d+", "idProject"="\d+"})
	 * @ParamConverter("user", options={"id"="id"})
	 * @ParamConverter("project", options={"id"="idProject"})
	 * @Security("is_granted('ROLE_USER_WRITE')")
	 */
	public function associateBtn(User $user, Project $project): Response
	{
		$userProject = $this->entityManager->getRepository(UserProject::class)->findOneBy([
			'user'    => $user->getId(),
			'project' => $project->getId(),
		]);

		$userProject->setDisabledAt(null);
		$this->entityManager->persist($userProject);
		$this->entityManager->flush();

		return $this->redirectToRoute('admin.user.show', [
			'id' => $user->getId(),
		]);
	}

	/**
	 * @Route("/desactive/user/{id}/project/{idProject}/dissociate", name="admin.user.project.dissociate", methods="GET", requirements={"id"="\d+", "idProject"="\d+"})
	 * @ParamConverter("user", options={"id"="id"})
	 * @ParamConverter("project", options={"id"="idProject"})
	 * @Security("is_granted('ROLE_USER_WRITE')")
	 *
	 * @param User $user
	 * @param Project $project
	 * @return Response
	 */
    public function dissociate(User $user, Project $project): Response
	{
		return $this->json([
			'html' => $this->renderView('admin/user/dissociate.html.twig', [
				'id' => $user->getId(),
				'idProject' => $project->getId(),
			]),
		]);
    }

	/**
	 * @Route("/desactive/user/{id}/project/{idProject}/dissociate/btn", name="admin.user.project.dissociate.btn", methods="GET", requirements={"id"="\d+", "idProject"="\d+"})
	 * @ParamConverter("user", options={"id"="id"})
	 * @ParamConverter("project", options={"id"="idProject"})
	 * @Security("is_granted('ROLE_USER_WRITE')")
	 *
	 * @param User $user
	 * @param Project $project
	 * @return Response
	 */
	public function dissociateBtn(User $user, Project $project): Response
	{
		$userProject = $this->entityManager->getRepository(UserProject::class)->findOneBy([
			'user'    => $user->getId(),
			'project' => $project->getId(),
		]);

		$userProject->setDisabledAt(new \DateTime());
		$userProject->setRate(null);
		$this->entityManager->persist($userProject);
		$this->entityManager->flush();

		return $this->redirectToRoute('admin.user.show', [
			'id' => $user->getId(),
		]);
	}

	/**
	 * @Route("/archive/{id}", name="admin.user.archive", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('USER_ARCHIVE', current_user)")
	 *
	 * @param Request $request
	 * @param User $current_user
	 * @return Response
	 */
    public function archive(Request $request, User $current_user): Response
	{
        $userProjects = $this->entityManager->getRepository(UserProject::class)->findBy(['user' => $current_user->getId(), 'disabledAt' => null]);
        foreach ($userProjects as $userProject) {
            $userProject->setDisabledAt(new \DateTime());
            //$project->setRate(null);
            $this->entityManager->persist($userProject);
        }

        $current_user->setDeletedAt(new \DateTime());
        //$user->setHasAccessEsm(false);
        //$user->setHasAccessEtmf(false);
        $this->entityManager->persist($current_user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.user.show', ['id' => $current_user->getId()]);
    }

	/**
	 * @Route("/desarchive/{id}", name="admin.user.desarchive", methods="GET", requirements={"id"="\d+"})
	 * @Security("is_granted('USER_RESTORE', current_user)")
	 *
	 * @param Request $request
	 * @param User $current_user
	 * @return Response
	 */
    public function desarchive(Request $request, User $current_user): Response
	{
        $current_user->setDeletedAt(null);
        $this->entityManager->persist($current_user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.user.show', ['id' => $current_user->getId()]);
    }

    /**
     * @Route("/admin/userproject/{id}/update-rate/", name="admin.userproject.rate.update", methods="POST", requirements={"id"="\d+"})
     * @Security("is_granted('USER_EDIT', userProject.getUser())")
     */
    public function updateRate(UserProject $userProject, Request $request): Response
    {
        $parametersAsArray = json_decode($request->getContent(), true);
        $userProject->setRate($parametersAsArray['rate']);
        $this->entityManager->persist($userProject);
        $this->entityManager->flush();

        return $this->json([
            'status' => 1,
        ]);
    }
}
