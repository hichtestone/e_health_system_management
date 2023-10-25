<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\ListGen\ReportVisitCenterList;
use App\ESM\Service\ListGen\ListGenFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/projects/{projectID}/center/report/visit")
 */
class ReportVisitController extends AbstractController
{
    /**
     * @Route("/", name="project.center.report.visit.index", methods="GET", requirements={"projectID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_VISIT_CENTER_LIST')")
     */
    public function index(Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $list = $lgm->getListGen(ReportVisitCenterList::class);

        return $this->render('center/report_visit_center/index.html.twig', [
            'project' => $project,
            'list' => $list->getList($project, $translator),
        ]);
    }

    /**
     * @Route("/ajax", name="project.center.report.visit.index.ajax", methods="GET", requirements={"projectID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_VISIT_CENTER_LIST')")
     */
    public function indexAjax(Request $request, Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $list = $lgm->getListGen(ReportVisitCenterList::class);
        $list = $list->getList($project, $translator);
        $list->setRequestParams($request->query);

        return $list->generateResponse();
    }

}
