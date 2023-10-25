<?php

namespace App\ESM\Controller\Project;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Exception;
use App\ESM\Entity\Project;
use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportConfig;
use App\ESM\Entity\ReportConfigVersionBlock;
use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Service\Office\WordGenerator;
use App\ESM\ListGen\ReportConfigVersionList;
use App\ESM\Service\ListGen\ListGenFactory;

/**
 * @Route("/projects/{projectID}/report/config/version")
 */
class ReportConfigController extends AbstractController
{
	/**
	 * @Route("/", name="project.settings.report.config.version.index", methods="GET", requirements={"projectID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_LIST')")
	 */
	public function index(Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(ReportConfigVersionList::class);

		return $this->render('project/settings/report_config/index.html.twig', [
			'project' => $project,
			'activeMenu2' => 'report-config',
			'list' => $list->getList($project, $translator),
		]);
	}

	/**
	 * @Route("/ajax", name="project.settings.report.config.version.index.ajax", methods="GET", requirements={"projectID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_LIST')")
	 */
	public function indexAjax(Request $request, Project $project, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(ReportConfigVersionList::class);
		$list = $list->getList($project, $translator);
		$list->setRequestParams($request->query);

		return $list->generateResponse();
	}

	/**
	 * @Route("/{reportConfigVersionID}/show", name="project.settings.report.config.version.show", options ={"expose"=true}, methods={"GET"}, requirements={"projectID"="\d+", "reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_SHOW', reportConfigVersion)")
	 */
	public function show(Project $project, ReportConfigVersion $reportConfigVersion)
	{
		return $this->render('project/settings/report_config/show.html.twig', [
			'reportConfigVersion' => $reportConfigVersion,
			'project' => $project,
		]);
	}

	/**
	 * @Route("/{reportConfigVersionID}/show/blocks", name="project.settings.report.config.version.show.blocks", options={"expose"=true}, methods={"GET"}, requirements={"projectID"="\d+", "reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_SHOW', reportConfigVersion)")
	 */
	public function listBlockParam(Project $project, ReportConfigVersion $reportConfigVersion, TranslatorInterface $translator): Response
	{
		$blocks                    = $histories = $config = [];
		$configVersion             = $this->getDoctrine()->getManager()->getRepository(ReportConfigVersion::class)->findOneBy(['config' => $reportConfigVersion->getConfig()], ['id' => 'DESC']);
		$reportConfigVersionBlocks = $this->getDoctrine()->getManager()->getRepository(ReportConfigVersionBlock::class)->findBy(['configVersion' => $reportConfigVersion]);

		foreach ($reportConfigVersionBlocks as $versionBlock) {
			$blocks[] = [
				'id' => $versionBlock->getId(),
				'active' => $versionBlock->isActive(),
				'number_version' => $versionBlock->getConfigVersion()->getNumberVersion(),
				'name' => $versionBlock->getBlock()->getName(),
				'sys' => $versionBlock->getBlock()->isSys(),
				'reportBlockID' => $versionBlock->getBlock()->getId(),
				'ordering' => $versionBlock->getOrdering(),
			];
		}

		$configVersions = $this->getDoctrine()->getManager()->getRepository(ReportConfigVersion::class)->findBy(['config' => $reportConfigVersion->getConfig()]);

		foreach ($configVersions as $configVersion) {
			$reportConfigVersionBlocks = $this->getDoctrine()->getManager()->getRepository(ReportConfigVersionBlock::class)->findBy(['configVersion' => $configVersion]);

			$histories[] = [
				'config' => $configVersion->getNumberVersion(),
				'version' => $configVersion->getConfig()->getModelVersion()->getNumberVersion(),
				'configuredBy' => $configVersion->getConfiguredBy()->displayName(),
				'startedAt' => $configVersion->getStartedAt(),
				'endedAt' => $configVersion->getEndedAt(),
				'block' => array_map(function ($reportConfigVersionBlock) {
					return [
						'name' => $reportConfigVersionBlock->getBlock()->getName(),
						'active' => $reportConfigVersionBlock->isActive(),
						'sys' => $reportConfigVersionBlock->getBlock()->isSys(),
					];
				}, $reportConfigVersionBlocks),
			];
		}

		$config[] = [
			'type' => $translator->trans(ReportModel::REPORT_TYPE[$configVersion->getModelType()]),
			'visit' => $translator->trans(ReportModel::VISIT_TYPE[$configVersion->getModelTypeVisit()]),
			'name' => $configVersion->getModelName(),
			'version' => $configVersion->getConfig()->getModelVersion()->getNumberVersion(),
			'config' => $reportConfigVersion->getNumberVersion(),
			'endedAt' => $reportConfigVersion->getEndedAt(),
		];

		$data['blocks']    = $blocks;
		$data['histories'] = $histories;
		$data['config']    = $config;

		return $this->json($data);
	}

	/**
	 * @Route("/{reportConfigVersionID}/show/blocks/edit", name="project.settings.report.config.version.show.blocks.edit", options={"expose"=true}, methods={"POST"}, requirements={"projectID"="\d+", "reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_EDIT', reportConfigVersion)")
	 * @throws Exception
	 */
	public function editBlockParam(Project $project, Request $request, ReportConfigVersion $reportConfigVersion): Response
	{
		$status = 'KO';
		$msg    = 'Error';

		$entityManager = $this->getDoctrine()->getManager();

		$json = json_decode($request->getContent(), true);
		if ($json) {
			$reportConfigVersions = $entityManager->getRepository(ReportConfigVersion::class)->findBy(['config' => $reportConfigVersion->getConfig()]);

			// update to set endedAt old version config
			foreach ($reportConfigVersions as $configVersion) {
				$configVersion->setEndedAt(new \DateTime());
				$configVersion->setStatus(ReportConfigVersion::STATUS_INACTIVE);
				$entityManager->persist($configVersion);
			}

			// new version config
			$reportConfigVersionEntity = new ReportConfigVersion();
			$reportConfigVersionEntity->setStatus(ReportConfigVersion::STATUS_ACTIVE);
			$reportConfigVersionEntity->setConfig($reportConfigVersion->getConfig());
			$reportConfigVersionEntity->setNumberVersion(count($reportConfigVersions) + 1);
			$reportConfigVersionEntity->setConfiguredBy($this->getUser());
			$reportConfigVersionEntity->setStartedAt(new \DateTime());
			$entityManager->persist($reportConfigVersionEntity);

			foreach ($json['data'] as $data) {
				$reportConfigVersionBlock = new ReportConfigVersionBlock();
				$reportConfigVersionBlock->setVersion($reportConfigVersionEntity);

				$reportConfigBlock = $entityManager->getRepository(ReportBlock::class)->find($data['reportBlockID']);
				$reportConfigVersionBlock->setBlock($reportConfigBlock);

				$reportConfigVersionBlock->setActive($data['active']);
				$reportConfigVersionBlock->setOrdering($data['ordering']);
				$entityManager->persist($reportConfigVersionBlock);
			}

			$status = 'OK';
			$msg    = 'Success';
		}

		$entityManager->flush();


		return $this->json([
			'status' => $status,
			'msg' => $msg,
		]);
	}

	/**
	 * @Route("/{reportConfigID}/activate/{reportConfigVersionID}", name="project.settings.report.config.version.activate", requirements={"projectID"="\d+", "reportConfigID"="\d+", "reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfig", options={"id"="reportConfigID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_ACTIVATE', reportConfigVersion)")
	 *
	 * @throws Exception
	 */
	public function activate(Project $project, ReportConfig $reportConfig, ReportConfigVersion $reportConfigVersion): RedirectResponse
	{
		$entityManager = $this->getDoctrine()->getManager();

		$reportConfigVersion->setStatus(ReportConfigVersion::STATUS_ACTIVE);
		$entityManager->persist($reportConfigVersion);
		$entityManager->flush();

		return $this->redirectToRoute('project.settings.report.config.version.index', [
			'projectID' => $project->getId(),
		]);
	}

	/**
	 * @Route("/{reportConfigID}/deactivate/{reportConfigVersionID}", name="project.settings.report.config.version.deactivate", requirements={"projectID"="\d+", "reportConfigID"="\d+", "reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfig", options={"id"="reportConfigID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_DEACTIVATE', reportConfigVersion)")
	 *
	 * @throws Exception
	 */
	public function deactivate(Project $project, ReportConfig $reportConfig, ReportConfigVersion $reportConfigVersion): RedirectResponse
	{
		$entityManager = $this->getDoctrine()->getManager();

		$reportConfigVersion->setStatus(ReportConfigVersion::STATUS_INACTIVE);
		$entityManager->persist($reportConfigVersion);
		$entityManager->flush();

		return $this->redirectToRoute('project.settings.report.config.version.index', [
			'projectID' => $project->getId(),
		]);
	}

    /**
     * @Route("/{reportConfigVersionID}/block/{reportConfigVersionBlockID}/activate", name="project.settings.report.config.version.block.activate", requirements={"projectID"="\d+", "reportConfigVersionID"="\d+", "reportConfigVersionBlockID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
     * @ParamConverter("reportConfigVersionBlock", options={"id"="reportConfigVersionBlockID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_ACTIVATE', reportConfigVersion)")
     *
     * @throws Exception
     */
    public function activateBlock(Project $project, ReportConfigVersion $reportConfigVersion, ReportConfigVersionBlock $reportConfigVersionBlock)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reportConfigVersionBlock->setActive(true);
        $entityManager->persist($reportConfigVersionBlock);
        $entityManager->flush();

        return $this->redirectToRoute('project.settings.report.config.version.show', [
            'projectID' => $project->getId(),
            'reportConfigVersionID' => $reportConfigVersion->getId(),
        ]);
    }

    /**
     * @Route("/{reportConfigVersionID}/block/{reportConfigVersionBlockID}/deactivate", name="project.settings.report.config.version.block.deactivate", requirements={"projectID"="\d+", "reportConfigVersionID"="\d+", "reportConfigVersionBlockID"="\d+"})
     * @ParamConverter("project", options={"id"="projectID"})
     * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
     * @ParamConverter("reportConfigVersionBlock", options={"id"="reportConfigVersionBlockID"})
     * @Security("is_granted('PROJECT_ACCESS', project) && is_granted('REPORT_CONFIG_ACTIVATE', reportConfigVersion)")
     *
     * @throws Exception
     */
    public function deactivateBlock(Project $project, ReportConfigVersion $reportConfigVersion, ReportConfigVersionBlock $reportConfigVersionBlock)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reportConfigVersionBlock->setActive(false);
        $entityManager->persist($reportConfigVersionBlock);
        $entityManager->flush();

        return $this->redirectToRoute('project.settings.report.config.version.show', [
            'projectID' => $project->getId(),
            'reportConfigVersionID' => $reportConfigVersion->getId(),
        ]);
    }


	/**
	 * @Route("/{reportConfigVersionID}/generate", name="admin.report.config.version.generate", requirements={"reportConfigVersionID"="\d+"})
	 * @ParamConverter("project", options={"id"="projectID"})
	 * @ParamConverter("reportConfigVersion", options={"id"="reportConfigVersionID"})
	 */
	public function generateWordConfigVersion(ReportConfigVersion $reportConfigVersion, WordGenerator $wordGenerator): BinaryFileResponse
	{
		$documentsReportsDir = $this->getParameter('DOCUMENTS_REPORTS_CONFIG_PATH');
		$fileName 			 = $wordGenerator->generateVersion($reportConfigVersion);

		$response = new BinaryFileResponse($documentsReportsDir . $fileName);
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$fileName);

		return $response;
	}
}
