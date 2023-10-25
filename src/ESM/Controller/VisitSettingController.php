<?php

namespace App\ESM\Controller;

use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\Entity\Visit;
use App\ESM\FormHandler\VisitSettingHandler;
use App\ESM\ListGen\VisitSettingList;
use App\ESM\Service\ListGen\ListGenFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects")
 */
class VisitSettingController extends AbstractController
{
    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}/visit-settings", name="project.list.visit.setting", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('VISITSETTING_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        $list = $lgm->getListGen(VisitSettingList::class);

        return $this->render('visitSetting/index.html.twig', [
            'list' => $list->getList($project, $this->translator),
            'project' => $project,
        ]);
    }

	/**
	 * @Route("/{id}/visit-settings/ajax", name="project.visit.setting.index.ajax", requirements={"id"="\d+"})
	 * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('VISITSETTING_LIST')")
	 *
	 * @param Request $request
	 * @param ListGenFactory $lgm
	 * @param Project $project
	 * @return Response
	 */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project): Response
	{
        // listgen handle request
        $list = $lgm->getListGen(VisitSettingList::class);
        $list = $list->getList($project, $this->translator);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 *
	 * @Route("/{id}/visit-settings/new", name="project.visit.setting.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_CREATE')")
	 *
	 * @param Request $request
	 * @param VisitSettingHandler $visitSettingHandler
	 * @param Project $project
	 * @return RedirectResponse|Response
	 */
    public function new(Request $request, VisitSettingHandler $visitSettingHandler, Project $project)
    {
        $visit = new Visit();
        $visit->setProject($project);

        if ($visitSettingHandler->handle($request, $visit, ['project' => $project, 'variable' => ''])) {
            return $this->redirectToRoute('project.list.visit.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('visitSetting/create.html.twig', [
            'form' => $visitSettingHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

	/**
	 * @Route("/{id}/visit-settings/{idVisit}/edit", name="project.visit.setting.edit", requirements={"id"="\d+", "idVisit"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("visit", options={"id"="idVisit"})
	 * @Security("is_granted('VISITSETTING_EDIT', visit)")
	 *
	 * @param Request $request
	 * @param VisitSettingHandler $visitSettingHandler
	 * @param Project $project
	 * @param Visit $visit
	 * @return Response
	 */
    public function edit(Request $request, VisitSettingHandler $visitSettingHandler, Project $project, Visit $visit): Response
    {
        $idPatientVariable = $this->entityManager->getRepository(Visit::class)->getVisitVariable($visit->getId());

        if ($visitSettingHandler->handle($request, $visit, ['project' => $project, 'variable' => $idPatientVariable])) {
            return $this->redirectToRoute('project.list.visit.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('visitSetting/create.html.twig', [
            'form' => $visitSettingHandler->createView(),
            'edit' => true,
        ]);
    }

    /**
     * @Route("/{id}/visit-settings/{idVisit}/archive", name="project.visit.setting.archive", requirements={"id"="\d+", "idVisit"="\d+"})
     * @ParamConverter("visit", options={"id"="idVisit"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_ARCHIVE', visit)")
     */
    public function archive(Visit $visit, Project $project): Response
    {
        $idPatientVariable = $this->entityManager->getRepository(Visit::class)->getPatientVariableByVisit($visit->getId());

        $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
        $patientVariable->setIsVisit(0);
        $patientVariable->setHasPatient(0);
        $patientVariable->setHasVisit(0);
        $this->entityManager->persist($patientVariable);

        $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($project->getId());
        foreach ($idsPatientData as $idPatientData) {
            if ($idPatientVariable === $idPatientData['idVariable']) {
                $entity = $this->entityManager->getRepository(PatientData::class)->find($idPatientData['id']);
                $entity->setDisabledAt(new \DateTime());
                $this->entityManager->persist($entity);
            }
        }

        $visit->setDeletedAt(new \DateTime());
        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.visit.setting', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/{id}/visit-settings/{idVisit}/restore", name="project.visit.setting.restore", requirements={"id"="\d+", "idVisit"="\d+"})
     * @ParamConverter("visit", options={"id"="idVisit"})
     * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_RESTORE', visit)")
     */
    public function restore(Visit $visit, Project $project): Response
    {
        $idPatientVariable = $this->entityManager->getRepository(Visit::class)->getPatientVariableByVisit($visit->getId());

        $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
        $patientVariable->setIsVisit(1);
        $patientVariable->setHasPatient(1);
        $patientVariable->setHasVisit(1);
        $this->entityManager->persist($patientVariable);

        $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($project->getId());
        foreach ($idsPatientData as $idPatientData) {
            if ($idPatientVariable === $idPatientData['idVariable']) {
                $entity = $this->entityManager->getRepository(PatientData::class)->find($idPatientData['id']);
                $entity->setDisabledAt(null);
                $this->entityManager->persist($entity);
            }
        }

        $visit->setDeletedAt(null);
        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.visit.setting', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/{id}/visits/json", name="project.visits.json", methods="GET", requirements={"id"="\d+"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('VISITSETTING_LIST')")
     */
    public function jsonIndex(Project $project): Response
    {
        $visits = $this->entityManager->getRepository(Visit::class)
            ->findByProject($project);

        return $this->json(
            array_map(function ($visit) {
                return [
                    'id' => $visit->getId(),
                    'label' => $visit->getLabel(),
                    'phase' => [
                        'id' => $visit->getPhase()->getId(),
                        'label' => $visit->getPhase()->getLabel(),
                    ],
                ];
            }, $visits)
        );
    }

    /**
     * @Route("/{id}/variables/json", name="project.list.variable.json", methods="GET", requirements={"id"="\d+"})
     */
    public function jsonVariablesIndex(Project $project): Response
    {
        $variables = $this->entityManager->getRepository(PatientVariable::class)
            ->findBy(['project' => $project, 'deletedAt' => null], ['label' => 'ASC']);

        $variables = $this->json(
            array_map(function ($variable) {
                return [
                    'id' => $variable->getId(),
                    'label' => $variable->getLabel(),
                ];
            }, $variables)
        );

        return $variables;
    }

	/**
	 * @Route("/{id}/visit-settings/{idVisit}/clone", name="project.visit.setting.clone", requirements={"id"="\d+", "idVisit"="\d+"})
	 * @ParamConverter("visit", options={"id"="idVisit"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('VISITSETTING_CREATE', visit)")
	 */
	public function cloneVisit(Visit $visit, Project $project): Response
	{
		$newVisit = clone $visit;

		$this->entityManager->persist($newVisit);
		$this->entityManager->flush();

		return $this->redirect($this->generateUrl("project.list.visit.setting", ['id' => $project->getId()]));
	}
}
