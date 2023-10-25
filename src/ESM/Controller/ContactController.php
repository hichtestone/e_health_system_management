<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\ContactHandler;
use App\ESM\ListGen\ContactList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class ContactController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 * @Route("/{id}/contacts", name="project.list.contacts", methods="GET", requirements={"id":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CONTACT_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen
        $list = $lgm->getListGen(ContactList::class);

        return $this->render('contact/index.html.twig', [
            'list' => $list->getList($request->getLocale(), $project),
            'project' => $project,
        ]);
    }

	/**
	 * @Route("/{id}/ajax/contacts", name="project.contact.index.ajax", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CONTACT_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen handle request
        $list = $lgm->getListGen(ContactList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 *  @Route("/{id}/contacts/new/{from}", name="project.contact.new", requirements={"id"="\d+", "from"="interlocutor|intervenant"}, defaults={"from" = null})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('CONTACT_CREATE')")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param ContactHandler $contactHandler
	 * @param string|null $from
	 * @return Response
	 */
    public function create(Request $request, Project $project, ContactHandler $contactHandler, ?string $from): Response
    {
        $contact = new Contact();
        $contact->setProject($project);

        if ($contactHandler->handle($request, $contact, [
            'project' => $project,
            'from' => $from,
            'user' => $this->getUser(),
			'users' => [],
			'interlocutors' => [],
        ])) {
            return $this->redirectToRoute('project.contact.show', [
                'id' => $project->getId(),
                'idContact' => $contact->getId(),
            ]);
        }

        return $this->render('contact/create.html.twig', [
            'form' => $contactHandler->createView(),
            'edit' => false,
            'from' => $from,
            'lang' => $this->getUser()->getLocale(),
        ]);
    }

	/**
	 * @Route("/{id}/contacts/{idContact}/show", name="project.contact.show", requirements={"id"="\d+", "idContact":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("contact", options={"id"="idContact"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CONTACT_SHOW', contact)")
	 *
	 * @param Project $project
	 * @param Contact $contact
	 * @return Response
	 */
    public function show(Project $project, Contact $contact) : Response
    {
        // render
        return $this->render('contact/show.html.twig', [
            'project' => $project,
            'contact' => $contact,
        ]);
    }

	/**
	 * @Route("/{id}/contacts/{idContact}/edit", name="project.contact.edit", requirements={"id"="\d+", "idContact"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("contact", options={"id"="idContact"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('CONTACT_EDIT', contact)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param Contact $contact
	 * @param ContactHandler $contactHandler
	 * @return Response
	 */
    public function edit(Request $request, Project $project, Contact $contact, ContactHandler $contactHandler): Response
    {
		$users = $this->getContactUsers($contact);
		$interlocutors = $this->getContactInterlocutors($contact);

		$options = ['project' => $project, 'edit' => true, 'users' => $users, 'interlocutors' => $interlocutors];

        if ($contactHandler->handle($request, $contact, $options)) {
            return $this->redirectToRoute('project.list.contacts', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('contact/create.html.twig', [
            'form' => $contactHandler->createView(),
            'edit' => true,
        ]);
    }

	/**
	 * @Route("/{id}/contacts/{idContact}/delete", name="project.contact.delete", requirements={"id"="\d+", "idContact"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("contact", options={"id"="idContact"})
	 * @Security("is_granted('CONTACT_DELETE', contact)")
	 *
	 * @param Project $project
	 * @param Contact $contact
	 * @return RedirectResponse
	 */
    public function delete(Project $project, Contact $contact) : RedirectResponse
    {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.contacts', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 * @param Contact $contact
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception|Exception
	 */
	private function getContactUsers(Contact $contact): array
	{
		$usersIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$contactUsers = $entityManager->getRepository(Contact::class)->findUserIds($contact);

		foreach ($contactUsers as $contactUser) {
			$usersIDs[] = $contactUser['id'];
		}

		return $usersIDs;
	}

	/**
	 * @param Contact $contact
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception|Exception
	 */
	private function getContactInterlocutors(Contact $contact): array
	{
		$interlocutorIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$interlocutorUsers = $entityManager->getRepository(Contact::class)->findInterlocutorIds($contact);

		foreach ($interlocutorUsers as $interlocutorUser) {
			$interlocutorIDs[] = $interlocutorUser['id'];
		}

		return $interlocutorIDs;
	}
}
