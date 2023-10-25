<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Meeting;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\MeetingHandler;
use App\ESM\ListGen\MeetingList;
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
class MeetingProjectController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	/**
	 * @Route("/{id}/meetings", name="project.list.meetings", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('MEETING_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen
        $list = $lgm->getListGen(MeetingList::class);

        return $this->render('meeting/index.html.twig', [
            'list' => $list->getList($request->getLocale(), $project),
            'project' => $project,
        ]);
    }

	/**
	 * @Route("/{id}/meetings/ajax/", name="project.meeting.index.ajax", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('MEETING_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return mixed
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        // listgen handle request
        $list = $lgm->getListGen(MeetingList::class);
        $list = $list->getList($request->getLocale(), $project);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/meetings/{idMeeting}/show", name="project.meeting.show", requirements={"id"="\d+", "idMeeting":"\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("meeting", options={"id"="idMeeting"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('MEETING_SHOW', meeting)")
	 *
	 * @param Project $project
	 * @param Meeting $meeting
	 * @return Response
	 */

    public function show(Project $project, Meeting $meeting): Response
    {
        // render
        return $this->render('meeting/show.html.twig', [
            'project' => $project,
            'meeting' => $meeting,
        ]);
    }

	/**
	 * @Route("/{id}/meetings/new", name="project.meeting.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PROJECT_WRITE', project) and is_granted('MEETING_CREATE')")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param MeetingHandler $meetingHandler
	 * @return Response
	 */
    public function create(Request $request, Project $project, MeetingHandler $meetingHandler): Response
    {
        $meeting = new Meeting();
        $meeting->setProject($project);
        $meeting->setCreatedAt(new \DateTime());
        $meeting->setCreatedBy($this->getUser());

		$options = ['project' => $project, 'users' => []];

        if ($meetingHandler->handle($request, $meeting, $options)) {
            return $this->redirectToRoute('project.meeting.show', [
                'id' => $project->getId(),
                'idMeeting' => $meeting->getId(),
            ]);
        }

        return $this->render('meeting/create.html.twig', [
            'form' => $meetingHandler->createView(),
            'edit' => false,
        ]);
    }

	/**
	 * @Route("/{id}/meetings/{idMeeting}/edit", name="project.meeting.edit", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("meeting", options={"id"="idMeeting"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('MEETING_EDIT', meeting)")
	 *
	 * @param Request $request
	 * @param Project $project
	 * @param Meeting $meeting
	 * @param MeetingHandler $meetingHandler
	 * @return Response
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function edit(Request $request, Project $project, Meeting $meeting, MeetingHandler $meetingHandler): Response
    {
		$users = $this->getMeetingUsers($meeting);

		$options = ['project' => $project, 'users' => $users];

        if ($meetingHandler->handle($request, $meeting, $options)) {
            return $this->redirectToRoute('project.list.meetings', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('meeting/create.html.twig', [
            'form' => $meetingHandler->createView(),
            'edit' => true,
        ]);
    }

	/**
	 * @Route("/{id}/meetings/{idMeeting}/delete", name="project.meeting.delete", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("meeting", options={"id"="idMeeting"})
	 * @Security("is_granted('PROJECT_WRITE', project) and is_granted('MEETING_DELETE', meeting)")
	 *
	 * @param Project $project
	 * @param Meeting $meeting
	 * @return RedirectResponse
	 */
    public function delete(Project $project, Meeting $meeting): RedirectResponse
    {
        $this->entityManager->remove($meeting);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.meetings', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 * @param Meeting $meeting
	 * @return array
	 * @throws Exception
	 * @throws \Doctrine\DBAL\Exception
	 */
	private function getMeetingUsers(Meeting $meeting): array
	{
		$usersIDs = [];

		$entityManager = $this->getDoctrine()->getManager();

		$meetingUsers = $entityManager->getRepository(Meeting::class)->findUserIds($meeting);

		foreach ($meetingUsers as $meetingUser) {
			$usersIDs[] = $meetingUser['id'];
		}

		return $usersIDs;
	}
}
