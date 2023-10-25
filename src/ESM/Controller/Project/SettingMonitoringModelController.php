<?php

namespace App\ESM\Controller\Project;

use App\ESM\Entity\Project;
use App\ESM\ListGen\Admin\ReportMonitoringList;
use App\ESM\Service\ListGen\ListGenFactory;
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
class SettingMonitoringModelController extends AbstractController
{
    /**
     * @Route("/{id}/report/model/", name="project.report.model.index", requirements={"id"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     * @Security("is_granted('ROLE_CONFIGURATION_MONITORING_MODEL')")
     */
    public function index(Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
    {
        $reportModelList = $lgm->getListGen(ReportMonitoringList::class);

        return $this->render('project/report/model/index.html.twig', [
            'project' => $project,
            'list' => $reportModelList->getList($project, $translator),
        ]);
    }

    /**
     * @Route("/{id}/report/model/ajax", name="project.report.model.index.ajax", requirements={"id"="\d+"}))
     * @Security("is_granted('ROLE_CONFIGURATION_MONITORING_MODEL')")
     */
    public function indexAjax(Request $request, ListGenFactory $lgm, Project $project, TranslatorInterface $translator): Response
    {
        $reportModelList = $lgm->getListGen(ReportMonitoringList::class);
        $reportModelList = $reportModelList->getList($project, $translator);
        $reportModelList->setRequestParams($request->query);

        return $reportModelList->generateResponse();
    }

    /**
     * @Route("/{id}/report/model/{idMonitoringModel}/fiche", name="project.setting.monitoring_model.fiche", requirements={"id"="\d+", "idMonitoringModel"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     */
    public function fiche(Project $project): array
    {
        return [];
    }

    /**
     * @Route("/{id}/report/model/{idMonitoringModel}/edit", name="project.setting.monitoring_model.edit", requirements={"id"="\d+", "idMonitoringModel"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     */
    public function edit(Project $project): array
    {
        return [];
    }

    /**
     * @Route("/{idMonitoringModel}/enable", name="project.setting.monitoring_model.enable", requirements={"id"="\d+", "idMonitoringModel"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     */
    public function enable(Project $project): array
    {
        return [];
    }

    /**
     * @Route("/{idMonitoringModel}/disable", name="project.setting.monitoring_model.disable",  requirements={"id"="\d+", "idMonitoringModel"="\d+"})
     * @ParamConverter("project", options={"id"="id"})
     */
    public function disable(Project $project): array
    {
        return [];
    }
}
