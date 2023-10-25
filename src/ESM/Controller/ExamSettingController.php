<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Exam;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\FormHandler\ExamSettingHandler;
use App\ESM\ListGen\ExamSettingList;
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
class ExamSettingController extends AbstractController
{
    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}/exam-settings", name="project.list.exam.setting", methods="GET", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('EXAMSETTING_LIST')")
     */
    public function index(Request $request, ListGenFactory $lgm, Project $project): Response
    {
        $list = $lgm->getListGen(ExamSettingList::class);

        return $this->render('examSetting/index.html.twig', [
            'list' => $list->getList($project, $this->translator),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/exam-settings/ajax", name="project.exam.setting.index.ajax", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('PROJECT_ACCESS', project) and is_granted('EXAMSETTING_LIST')")
     *
     * @return Response
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project)
    {
        // listgen handle request
        $list = $lgm->getListGen(ExamSettingList::class);
        $list = $list->getList($project, $this->translator);
        $list->setRequestParams($request->query);

        // json response
        return $list->generateResponse();
    }

	/**
	 *
	 * @Route("/{id}/exam-settings/new", name="project.exam.setting.new", requirements={"id"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_CREATE')")
	 *
	 * @param Request $request
	 * @param ExamSettingHandler $examSettingHandler
	 * @param Project $project
	 * @return RedirectResponse|Response
	 */
    public function new(Request $request, ExamSettingHandler $examSettingHandler, Project $project)
    {
        $exam = new Exam();
        $exam->setProject($project);

        if ($examSettingHandler->handle($request, $exam, ['project' => $project])) {
            return $this->redirectToRoute('project.list.exam.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('examSetting/create.html.twig', [
            'form' => $examSettingHandler->createView(),
            'project' => $project,
            'edit' => false,
        ]);
    }

	/**
	 *
	 * @Route("/{id}/exam-settings/{idExam}/edit", name="project.exam.setting.edit", requirements={"id"="\d+", "idExam"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("exam", options={"id"="idExam"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_EDIT', exam)")
	 *
	 * @param Request $request
	 * @param ExamSettingHandler $examSettingHandler
	 * @param Project $project
	 * @param Exam $exam
	 * @return Response
	 */
    public function edit(Request $request, ExamSettingHandler $examSettingHandler, Project $project, Exam $exam): Response
    {
        if ($examSettingHandler->handle($request, $exam, ['project' => $project])) {
            return $this->redirectToRoute('project.list.exam.setting', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('examSetting/create.html.twig', [
            'form' => $examSettingHandler->createView(),
            'edit' => true,
        ]);
    }

	/**
	 *
	 * @Route("/{id}/exam-settings/{idExam}/archive", name="project.exam.setting.archive", requirements={"id"="\d+", "idExam"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("exam", options={"id"="idExam"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_ARCHIVE', exam)")
	 *
	 * @param Exam $exam
	 * @param Project $project
	 * @return Response
	 */
    public function archive(Exam $exam, Project $project): Response
    {
        $idPatientVariable = $this->entityManager->getRepository(Exam::class)->getPatientVariableByExam($exam->getId());

        $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
        $patientVariable->setIsExam(0);
        $patientVariable->setHasPatient(0);
        $this->entityManager->persist($patientVariable);

        $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($project->getId());
        foreach ($idsPatientData as $idPatientData) {
            if ($idPatientVariable === $idPatientData['idVariable']) {
                $entity = $this->entityManager->getRepository(PatientData::class)->find($idPatientData['id']);
                $entity->setDisabledAt(new \DateTime());
                $this->entityManager->persist($entity);
            }
        }

        $exam->setDeletedAt(new \DateTime());
        $this->entityManager->persist($exam);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.exam.setting', [
            'id' => $project->getId(),
        ]);
    }

	/**
	 *
	 * @Route("/{id}/exam-settings/{idExam}/restore", name="project.exam.setting.restore", requirements={"id"="\d+", "idExam"="\d+"})
	 * @ParamConverter("project", options={"id"="id"})
	 * @ParamConverter("exam", options={"id"="idExam"})
	 * @Security("is_granted('PROJECT_ACCESS_AND_OPEN', project) and is_granted('EXAMSETTING_RESTORE', exam)")
	 *
	 * @param Exam $exam
	 * @param Project $project
	 * @return Response
	 */
    public function restore(Exam $exam, Project $project): Response
    {
        $idPatientVariable = $this->entityManager->getRepository(Exam::class)->getPatientVariableByExam($exam->getId());

        $patientVariable = $this->entityManager->getRepository(PatientVariable::class)->find($idPatientVariable);
        $patientVariable->setIsExam(1);
        $patientVariable->setHasPatient(1);

        $idsPatientData = $this->entityManager->getRepository(PatientData::class)->getAllPatientData($project->getId());
        foreach ($idsPatientData as $idPatientData) {
            if ($idPatientVariable === $idPatientData['idVariable']) {
                $entity = $this->entityManager->getRepository(PatientData::class)->find($idPatientData['id']);
                $entity->setDisabledAt(null);
                $this->entityManager->persist($entity);
            }
        }

        $exam->setDeletedAt(null);
        $this->entityManager->persist($exam);
        $this->entityManager->flush();

        return $this->redirectToRoute('project.list.exam.setting', [
            'id' => $project->getId(),
        ]);
    }
}
