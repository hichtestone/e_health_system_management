<?php

namespace App\ESM\Controller;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\PhaseSettingHandler;
use App\ESM\ListGen\PhaseSettingList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class PhaseSettingController extends AbstractController
{
    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}/phase-settings", name="project.list.phase.setting", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PHASESETTING_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        $list = $lgm->getListGen(PhaseSettingList::class);

        return $this->render('phaseSetting/index.html.twig', [
            'list' => $list->getList($project, $this->translator),
            'project' => $project,
        ]);
    }

	/**
	 * @Route("/{id}/phase-settings/ajax", name="project.phase.setting.index.ajax", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PHASESETTING_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        // listgen handle request
        $list = $lgm->getListGen(PhaseSettingList::class);
        $list = $list->getList($project, $this->translator);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 * @Route("/{id}/phase-settings/new", name="project.phase.setting.new", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_CREATE')")
	 * @param Request $request
	 * @param PhaseSettingHandler $phaseSettingHandler
	 * @param Project $project
	 * @return Response
	 */
    public function new(Request $request, PhaseSettingHandler $phaseSettingHandler, Project $project): Response
	{
        $phaseSetting = new PhaseSetting();
        $phaseSetting->setProject($project);

        if ($phaseSettingHandler->handle($request, $phaseSetting)) {
            return $this->redirectToRoute('project.list.phase.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('phaseSetting/create.html.twig', [
            'form' => $phaseSettingHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

    /**
     * @Route("/{id}/phase-settings/{idPhaseSetting}/edit", name="project.phase.setting.edit", requirements={"id"="\d+", "idPhaseSetting"="\d+"})
     * @ParamConverter("phaseSetting", options={"id"="idPhaseSetting"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_EDIT', phaseSetting)")
     */
    public function edit(Request $request, PhaseSettingHandler $phaseSettingHandler, Project $project, PhaseSetting $phaseSetting): Response
    {
        if ($phaseSettingHandler->handle($request, $phaseSetting)) {
            return $this->redirectToRoute('project.list.phase.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('phaseSetting/create.html.twig', [
            'form' => $phaseSettingHandler->createView(),
            'edit' => true,
        ]);
    }

    /**
     * @Route("/{id}/phase-settings/{idPhaseSetting}/archive", name="project.phase.setting.archive", requirements={"id"="\d+", "idPhaseSetting"="\d+"})
     * @ParamConverter("phaseSetting", options={"id"="idPhaseSetting"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_ARCHIVE', phaseSetting)")
     */
    public function archive(PhaseSetting $phaseSetting, Project $project): Response
    {
        $phaseSetting->setDeletedAt(new \DateTime());
        $this->entityManager->persist($phaseSetting);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.phase.setting', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/{id}/phase-settings/{idPhaseSetting}/restore", name="project.phase.setting.restore", requirements={"id"="\d+", "idPhaseSetting"="\d+"})
     * @ParamConverter("phaseSetting", options={"id"="idPhaseSetting"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('PHASESETTING_RESTORE', phaseSetting)")
     */
    public function restore(PhaseSetting $phaseSetting, Project $project): Response
    {
        $phaseSetting->setDeletedAt(null);
        $this->entityManager->persist($phaseSetting);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.phase.setting', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/{id}/phases/json", name="project.list.phase.json", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('PHASESETTING_LIST')")
     */
    public function jsonIndex(Project $project): Response
    {
        $visits = $this->entityManager->getRepository(PhaseSetting::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['label' => 'ASC']);

        return $this->json(
            array_map(function ($phase) {
                return [
                    'id' => $phase->getId(),
                    'label' => $phase->getLabel(),
                ];
            }, $visits)
        );
    }
}
