<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Center;
use App\ESM\Entity\Project;
use App\ESM\Entity\ReportVisit;
use App\ESM\FormHandler\ReportVisitHandler;
use App\ESM\ListGen\ReportVisitList;
use App\ESM\Service\ListGen\ListGenFactory;
use App\ESM\Service\Office\WordGenerator;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects/{projectID}/center/{centerID}/report/visit")
 */
class ReportVisitCenterController extends AbstractController
{
    /**
     * @Route("/", name="project.center.report.visit.center.index", methods="GET", requirements={"projectID"="\d+", "centerID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_VISIT_LIST')")
     */
    public function index(Project $project, Center $center, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $list = $lgm->getListGen(ReportVisitList::class);

        return $this->render('center/report_visit/index.html.twig', [
            'project' => $project,
            'center' => $center,
            'activeMenu2' => 'center',
            'list' => $list->getList($project, $center, $translator),
        ]);
    }

    /**
     * @Route("/ajax", name="project.center.report.visit.center.index.ajax", methods="GET", requirements={"projectID"="\d+", "centerID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_VISIT_LIST')")
     */
    public function indexAjax(Request $request, Project $project, Center $center, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $list = $lgm->getListGen(ReportVisitList::class);
        $list = $list->getList($project, $center, $translator);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

    /**
     * @Route("/new", name="project.center.report.visit.center.new", requirements={"projectID"="\d+", "centerID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_CREATE')")
     */
    public function new(Request $request, Project $project, Center $center, ReportVisitHandler $reportVisitHandler): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reportVisit = new ReportVisit();

        $reportVisit->setProject($project);
        $reportVisit->setCenter($center);

        $reportVisits = $entityManager->getRepository(ReportVisit::class)->findBy(['project' => $project, 'center' => $center]);

        $reportVisit->setNumberVisit(count($reportVisits) + 1);

        $options = ['project' => $project, 'report' => false, 'validate' => false, 'user' => $this->getUser()];

        if ($reportVisitHandler->handle($request, $reportVisit, $options)) {
            return $this->redirectToRoute('project.center.report.visit.center.index', [
                'projectID' => $project->getId(),
                'centerID' => $center->getId(),
            ]);
        }

        return $this->render('center/report_visit/create.html.twig', [
            'form' => $reportVisitHandler->createView(),
            'project' => $project,
            'center' => $center,
            'action' => 'create',
            'activeMenu2' => 'center',
            'options' => $options,
        ]);
    }

    /**
     * @Route("/{reportVisitID}/edit", name="project.center.report.visit.center.edit", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_EDIT', reportVisit)")
     */
    public function edit(Request $request, Project $project, Center $center, ReportVisit $reportVisit, ReportVisitHandler $reportVisitHandler): Response
    {
        $options = ['project' => $project, 'report' => false, 'validate' => false, 'user' => $this->getUser()];

        if ($reportVisitHandler->handle($request, $reportVisit, $options)) {
            return $this->redirectToRoute('project.center.report.visit.center.index', [
                'projectID' => $project->getId(),
                'centerID' => $center->getId(),
            ]);
        }

        return $this->render('center/report_visit/create.html.twig', [
            'form' => $reportVisitHandler->createView(),
            'project' => $project,
            'center' => $center,
            'reportVisit' => $reportVisit,
            'action' => 'edit',
            'activeMenu2' => 'center',
            'options' => $options,
        ]);
    }

    /**
     * @Route("/{reportVisitID}/report/new", name="project.center.report.visit.center.report.new", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_CREATE', reportVisit)")
     *
     * @throws Exception
     */
    public function report(Request $request, Project $project, Center $center, ReportVisit $reportVisit, ReportVisitHandler $reportVisitHandler): Response
    {
		$entityManager = $this->getDoctrine()->getManager();
        $reportVisit->setReportedBy($this->getUser());
        $code = ucfirst($project->getAcronyme()).'_'.$center->getNumber().'_'.str_pad($reportVisit->getNumberVisit(), 3, '0', STR_PAD_LEFT);
        $reportVisit->setCode($code);
        $reportVisit->setVisitStatus(ReportVisit::VISIT_STATUS_DONE);
        $reportVisit->setReportStatus(ReportVisit::REPORT_STATUS_IN_PROGRESS);

        $options = ['project' => $project, 'report' => true, 'validate' => false, 'user' => $this->getUser()];

        if ($reportVisitHandler->handle($request, $reportVisit, $options)) {
            return $this->redirectToRoute('project.center.report.visit.center.index', [
                'projectID' => $project->getId(),
                'centerID' => $center->getId(),
            ]);
        }

        return $this->render('center/report_visit/create.html.twig', [
            'form' => $reportVisitHandler->createView(),
            'project' => $project,
            'center' => $center,
            'reportVisit' => $reportVisit,
            'action' => 'create',
            'activeMenu2' => 'center',
            'options' => $options,
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/{reportVisitID}/no-visit", name="project.center.report.visit.center.noVisit", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_NO_VISIT', reportVisit)")
     *
     * @throws Exception
     */
    public function noVisit(Project $project, Center $center, ReportVisit $reportVisit): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reportVisit->setVisitStatus(ReportVisit::VISIT_STATUS_NO_DONE);

        $entityManager->persist($reportVisit);
        $entityManager->flush();

        return $this->redirectToRoute('project.center.report.visit.center.index', [
            'projectID' => $project->getId(),
            'centerID' => $center->getId(),
        ]);
    }

    /**
     * @Route("/{reportVisitID}/download-template", name="project.center.report.visit.center.download.template", methods="GET", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_DOWNLOAD', reportVisit)")
     */
    public function downloadTemplate(Project $project, Center $center, ReportVisit $reportVisit, WordGenerator $wordGenerator): BinaryFileResponse
    {
		$documentsReportsVisitTemplateDir = $this->getParameter('DOCUMENTS_REPORTS_VISIT_BEGIN_PATH');
		$fileName 			 			  = $wordGenerator->generateVersion($reportVisit->getReportConfigVersion(), true, $reportVisit);

		$response = new BinaryFileResponse($documentsReportsVisitTemplateDir . $fileName);
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$fileName);

		return $response;
    }

	/**
	 * @Route("/{reportVisitID}/delete", name="project.center.report.visit.center.delete", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("center", options={"id"="centerID"})
	 * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_DELETE', reportVisit)")
	 * @throws Exception
	 */
    public function delete(Project $project, Center $center, ReportVisit $reportVisit): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reportVisit->setReportedBy(null);
        $reportVisit->setValidatedBy(null);
        $reportVisit->setReportConfigVersion(null);
        $reportVisit->setReportType(null);
        $reportVisit->setVisitStatus(null);
        $reportVisit->setReportedAt(null);
        $reportVisit->setValidatedAt(null);
        $reportVisit->setCode(null);
        $reportVisit->setReportStatus(null);
		$reportVisit->setDeletedAt(new DateTime());

        $entityManager->persist($reportVisit);
        $entityManager->flush();

        return $this->redirectToRoute('project.center.report.visit.center.index', [
            'projectID' => $project->getId(),
            'centerID' => $center->getId(),
        ]);
    }

    /**
     * @Route("/{reportVisitID}/validate", name="project.center.report.visit.center.validate", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_VALIDATE', reportVisit)")
     *
     * @throws Exception
     */
    public function validate(Request $request, Project $project, Center $center, ReportVisit $reportVisit, ReportVisitHandler $reportVisitHandler): Response
    {
        $reportVisit->setReportStatus(ReportVisit::REPORT_STATUS_VALIDATE);
        $reportVisit->setValidatedAt(new \DateTime());

        $options = ['project' => $project, 'report' => false, 'validate' => true, 'user' => $this->getUser()];

        if ($reportVisitHandler->handle($request, $reportVisit, $options)) {
            return $this->redirectToRoute('project.center.report.visit.center.index', [
                'projectID' => $project->getId(),
                'centerID' => $center->getId(),
            ]);
        }

        return $this->render('center/report_visit/create.html.twig', [
            'form' => $reportVisitHandler->createView(),
            'project' => $project,
            'center' => $center,
            'reportVisit' => $reportVisit,
            'action' => 'edit',
            'activeMenu2' => 'center',
            'options' => $options,
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/{reportVisitID}/download-report", name="project.center.report.visit.center.download.report", requirements={"projectID"="\d+", "centerID"="\d+", "reportVisitID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("center", options={"id"="centerID"})
     * @ParamConverter("reportVisit", options={"id"="reportVisitID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('PROJECT_WRITE', project) && is_granted('REPORT_VISIT_REPORT_DOWNLOAD_REPORT', reportVisit)")
     */
    public function downloadReport(Project $project, Center $center, ReportVisit $reportVisit, WordGenerator $wordGenerator): BinaryFileResponse
    {
		$documentsReportsVisitTemplateDir = $this->getParameter('DOCUMENTS_REPORTS_VISIT_END_PATH');
		$fileName 			 			  = $reportVisit->getReportFile();

		$response = new BinaryFileResponse($documentsReportsVisitTemplateDir . $fileName);
		$response->headers->set('Content-Type', 'application/pdf');
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$fileName);

		return $response;
    }
}
