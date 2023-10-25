<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Exception;
use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportBlockParam;
use App\ESM\Entity\ReportConfig;
use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportConfigVersionBlock;
use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Entity\ReportModelVersionBlock;
use App\ESM\FormHandler\ReportModelHandler;
use App\ESM\ListGen\Admin\ReportModelVersionList;
use App\ESM\Service\ListGen\ListGenFactory;
use App\ESM\Service\Office\WordGenerator;

/**
 * @Route("/admin/report/model")
 */
class ReportModelController extends AbstractController
{
	/**
	 * @Route("/", name="admin.report.model.index", options={"expose"=true})
	 * @Security("is_granted('REPORT_MODEL_LIST')")
	 */
	public function index(ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		$list = $lgm->getListGen(ReportModelVersionList::class);

		$reportModelVersion = $this->getDoctrine()->getRepository(ReportModelVersion::class)->indexListGen();

		return $this->render('admin/report/model/index.html.twig', [
			'list' => $list->getList($translator),
		]);
	}

	/**
	 * @Route("/ajax/report/model/version", name="admin.report.model.version.index.ajax")
	 *
	 * @Security("is_granted('REPORT_MODEL_LIST')")
	 */
	public function indexAjax(Request $request, ListGenFactory $lgm, TranslatorInterface $translator): Response
	{
		// listgen handle request
		$list = $lgm->getListGen(ReportModelVersionList::class);
		$list = $list->getList($translator);
		$list->setRequestParams($request->query);

		// json response
		return $list->generateResponse();
	}

	/**
	 * @Route("/new", name="admin.report.model.new")
	 *
	 * @Security("is_granted('REPORT_MODEL_CREATE')")
	 */
	public function new(Request $request, ReportModelHandler $reportModelHandler): Response
	{
		$reportModel = new ReportModel();

		if ($reportModelHandler->handle($request, $reportModel)) {

			$entityManager = $this->getDoctrine()->getManager();

			$reportModelVersion = new ReportModelVersion();

			$versions = $entityManager->getRepository(ReportModelVersion::class)->findBy(['reportModel' => $reportModel]);

			$reportModelVersion->setStatus(ReportModelVersion::STATUS_CREATE);
			$reportModelVersion->setNumberVersion(count($versions) + 1);
			$reportModelVersion->setCreatedAt(new \DateTime());
			$reportModelVersion->setCreatedby($this->getUser());
			$reportModelVersion->setReportModel($reportModel);
			$entityManager->persist($reportModelVersion);

			// add default system blocks word
			$blocksSystem = $entityManager->getRepository(ReportBlock::class)->getBlockSystemByModel($reportModel->getReportType());
			$order        = 1;
			foreach ($blocksSystem as $blockSystem) {
				$reportVersionBlock = new ReportModelVersionBlock();
				$reportVersionBlock->setVersion($reportModelVersion);
				$reportVersionBlock->setBlock($blockSystem);
				$reportVersionBlock->setOrdering($order);
				++$order;
				$entityManager->persist($reportVersionBlock);
				$entityManager->flush();

				$reportModelVersion->addVersionBlock($reportVersionBlock);
			}

			$entityManager->persist($reportModelVersion);
			$entityManager->flush();

			return $this->redirectToRoute('admin.report.model.index');
		}

		return $this->render('admin/report/model/create.html.twig', [
			'form' => $reportModelHandler->createView(),
			'action' => 'create',
		]);
	}

	/**
	 * @Route("/version/show/{reportModelVersionID}", name="admin.report.model.version.show", options={"expose"=true}, methods={"GET"}, requirements={"reportModelVersionID"="\d+"})
	 * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
	 *
	 * @Security("is_granted('REPORT_MODEL_VERSION_SHOW', reportModelVersion)")
	 */
	public function show(ReportModelVersion $reportModelVersion): Response
	{
		$versionBlocks = $this->getDoctrine()->getRepository(ReportModelVersionBlock::class)->findBy(['version' => $reportModelVersion], ['ordering' => 'ASC']);

		$canEdit = $reportModelVersion->getReportModel()->hasPublishedVersion();

		return $this->render('admin/report/model/show.html.twig', [
			'reportModelVersion' => $reportModelVersion,
			'versionBlocks' => $versionBlocks,
			'canEdit' => !$canEdit,
		]);
	}

	/**
	 * @Route("/publication", name="admin.report.model.publication", options={"expose"=true}, methods={"POST"})
	 */
	public function publication(Request $request): Response
	{
		$reportModelVersionIDs = $request->get('reportModelVersionIDs');
		$entityManager         = $this->getDoctrine()->getManager();
		$reportModelsVersions  = $entityManager->getRepository(ReportModelVersion::class)->findById($reportModelVersionIDs);
		$messageErrors         = [];
		$messageStatus         = 'OK';


		foreach ($reportModelsVersions as $reportModelVersion) {
			try {
				if (ReportModelVersion::STATUS_CREATE !== $reportModelVersion->getStatus() || $reportModelVersion->getPublishedAt() || $reportModelVersion->getPublishedBy()) {
					throw new Exception('La version du model a déjà été publiée.');
				}

				$reportModelVersionsToBeObsolete = $entityManager->getRepository(ReportModelVersion::class)->findBy(['reportModel' => $reportModelVersion->getReportModel()]);

				foreach ($reportModelVersionsToBeObsolete as $reportModelVersionToBeObsolete) {
					if ($reportModelVersionToBeObsolete->getId() !== $reportModelVersion->getId()) {
						$reportModelVersionToBeObsolete->setStatus(ReportModelVersion::STATUS_OBSOLETE);
						$reportModelVersionToBeObsolete->setObsoletedAt(new \DateTime());
						$reportModelVersionToBeObsolete->setObsoletedBy($this->getUser());

						$entityManager->persist($reportModelVersion);

						$projects = $entityManager->getRepository(Project::class)->findAll();
						foreach ($projects as $project) {
							$reportConfigs = $entityManager->getRepository(ReportConfig::class)->findBy(['modelVersion' => $reportModelVersionToBeObsolete]);

							foreach ($reportConfigs as $config) {
								$reportConfigVersionsToBeInactive = $entityManager->getRepository(ReportConfigVersion::class)->findBy(['config' => $config]);
								foreach ($reportConfigVersionsToBeInactive as $reportConfigVersionToBeInactive) {
									$reportConfigVersionToBeInactive->setStatus(ReportConfigVersion::STATUS_INACTIVE);
									if ($reportConfigVersionToBeInactive->getEndedAt() == null) {
										$reportConfigVersionToBeInactive->setEndedAt(new \DateTime());
									}

									$entityManager->persist($reportConfigVersionToBeInactive);
								}
							}
						}
					}
				}

				$reportModelVersion->setStatus(ReportModelVersion::STATUS_PUBLISH);
				$reportModelVersion->setPublishedAt(new \DateTime());
				$reportModelVersion->setPublishedBy($this->getUser());
				$entityManager->persist($reportModelVersion);

				// available reportModelVersion for all project and reportConfig
				$projects = $entityManager->getRepository(Project::class)->findAll();
				foreach ($projects as $project) {
					$reportConfig = new ReportConfig();
					$reportConfig->setModelVersion($reportModelVersion);
					$reportConfig->setProject($project);

					$entityManager->persist($reportConfig);

					$reportConfigVersion = new ReportConfigVersion();
					$reportConfigVersion->setConfig($reportConfig);
					$reportConfigVersion->setStatus(ReportConfigVersion::STATUS_ACTIVE);
					$reportConfigVersion->setNumberVersion(1);
					$reportConfigVersion->setConfiguredBy($this->getUser());
					$reportConfigVersion->setStartedAt(new \DateTime());

					$entityManager->persist($reportConfigVersion);

					$reportModelVersionBlocksSystems = [];

					$reportModelVersionBlocks = $entityManager->getRepository(ReportModelVersionBlock::class)->findBy(['version' => $reportModelVersion]);
					$order                    = 1;
					foreach ($reportModelVersionBlocks as $reportModelVersionBlock) {
						$reportConfigVersionBlock = new ReportConfigVersionBlock();
						$reportConfigVersionBlock->setVersion($reportConfigVersion);
						$reportConfigVersionBlock->setBlock($reportModelVersionBlock->getBlock());
						$reportConfigVersionBlock->setOrdering($reportModelVersionBlock->getOrdering());
						$reportConfigVersionBlock->setActive(true);
						++$order;

						$entityManager->persist($reportConfigVersionBlock);

						if ($reportModelVersionBlock->getBlock()->isSys()) {
							$reportModelVersionBlocksSystems[] = $reportModelVersionBlock;
						}
					}

				}

				$entityManager->flush();
			} catch (Exception $exception) {
				$messageStatus   = 'KO';
				$messageErrors[] = 'Publication impossible. La version ' . $reportModelVersion->getNumberVersion() . ' du modele ' . $reportModelVersion->getReportModel()->getName() . ' n\'a pas pu être publiée !';
			}
		}

		return $this->json([
			'messageStatus' => $messageStatus,
			'messageErrors' => $messageErrors,
		]);
	}

	/**
	 * @Route("/outdated", name="admin.report.model.outdated", options={"expose"=true}, methods={"POST"})
	 */
	public function outdated(Request $request): Response
	{
		$reportModelVersionIDs = $request->get('reportModelVersionIDs');
		$reportModelsVersions  = $this->getDoctrine()->getRepository(ReportModelVersion::class)->findById($reportModelVersionIDs);
		$entityManager         = $this->getDoctrine()->getManager();
		$messageErrors         = [];
		$messageStatus         = 'OK';

		foreach ($reportModelsVersions as $reportModelsVersion) {
			try {
				if (ReportModelVersion::STATUS_PUBLISH !== $reportModelsVersion->getStatus() || !$reportModelsVersion->getPublishedAt() || !$reportModelsVersion->getPublishedBy()) {
					throw new Exception('La version du model n\'est pas en status publiée.');
				}

				$reportModelsVersion->setStatus(ReportModelVersion::STATUS_OBSOLETE);
				$reportModelsVersion->setObsoletedAt(new \DateTime());
				$reportModelsVersion->setObsoletedBy($this->getUser());
				$entityManager->persist($reportModelsVersion);

				$projects = $entityManager->getRepository(Project::class)->findAll();
				foreach ($projects as $project) {
					$reportConfigs = $entityManager->getRepository(ReportConfig::class)->findBy(['modelVersion' => $reportModelsVersion]);

					foreach ($reportConfigs as $config) {
						$reportConfigVersionToBeInactive = $entityManager->getRepository(ReportConfigVersion::class)->findOneBy(['config' => $config]);
						$reportConfigVersionToBeInactive->setStatus(ReportConfigVersion::STATUS_INACTIVE);

						$entityManager->persist($reportConfigVersionToBeInactive);
					}
				}

				$entityManager->flush();
			} catch (Exception $exception) {
				$messageStatus   = 'KO';
				$messageErrors[] = 'La version ' . $reportModelsVersion->getNumberVersion() . ' du modele ' . $reportModelsVersion->getReportModel()->getName() . ' n\'a pas pu être rendu obsolète !';
			}
		}

		return $this->json([
			'messageStatus' => $messageStatus,
			'messageErrors' => $messageErrors,
		]);
	}

	/**
	 * @Route ("/edit/{reportModelID}/{reportModelVersionID}", name="admin.report.model.edit", requirements={"reportModelID"="\d+", "reportModelVersionID"="\d+"})
	 * @ParamConverter ("reportModel", options={"id"="reportModelID"})
	 * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
	 * @Security("is_granted('REPORT_MODEL_EDIT', reportModel)")
	 *
	 * @throws Exception|Exception
	 */
	public function edit(Request $request, ReportModel $reportModel, ReportModelVersion $reportModelVersion, ReportModelHandler $reportModelHandler): Response
	{
		if ($reportModelHandler->handle($request, $reportModel)) {
			return $this->redirectToRoute('admin.report.model.version.show', [
				'reportModelVersionID' => $reportModelVersion->getId(),
			]);
		}

		return $this->render('admin/report/model/create.html.twig', [
			'form' => $reportModelHandler->createView(),
			'action' => 'edit',
		]);
	}

	/**
	 * @Route ("/{reportModelID}/delete", name="admin.report.model.delete", requirements={"reportModelID"="\d+"})
	 * @ParamConverter ("reportModel", options={"id"="reportModelID"})
	 * @Security("is_granted('REPORT_MODEL_DELETE', reportModel)")
	 */
	public function delete(ReportModel $reportModel)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$reportModel->setDeletedAt(new \DateTime());

		$entityManager->persist($reportModel);
		$entityManager->flush();

		return $this->redirectToRoute('admin.report.model.index');
	}

	/**
	 * @Route("/{reportModelID}/version/new", name="admin.report.model.version.new", requirements={"reportModelID"="\d+"})
	 * @ParamConverter("reportModel", options={"id"="reportModelID"})
	 * @Security("is_granted('REPORT_MODEL_CREATE_VERSION', reportModel)")
	 */
	public function newVersion(ReportModel $reportModel): Response
	{
		$entityManager = $this->getDoctrine()->getManager();

		$versions = $entityManager->getRepository(ReportModelVersion::class)->findBy(['reportModel' => $reportModel]);

		$reportModelVersion = new ReportModelVersion();
		$reportModelVersion->setStatus(ReportModelVersion::STATUS_CREATE);
		$reportModelVersion->setNumberVersion(count($versions) + 1);
		$reportModelVersion->setCreatedAt(new \DateTime());
		$reportModelVersion->setCreatedby($this->getUser());
		$reportModelVersion->setReportModel($reportModel);
		$entityManager->persist($reportModelVersion);

		if ($versions) {
			foreach ($versions as $version) {
				if (ReportModelVersion::STATUS_PUBLISH == $version->getStatus()) {
					$reportModelVersionsPublished = $entityManager->getRepository(ReportModelVersionBlock::class)->findBy(['version' => $version]);
					if ($reportModelVersionsPublished) {
						$order = 1;
						foreach ($reportModelVersionsPublished as $reportModelVersionPublished) {
							if ($reportModelVersionPublished) {
								if ($reportModelVersionPublished->getBlock()->isSys()) {
									// add all default system block word
									$reportVersionBlock = new ReportModelVersionBlock();
									$reportVersionBlock->setVersion($reportModelVersion);
									$reportVersionBlock->setBlock($reportModelVersionPublished->getBlock());
									$reportVersionBlock->setOrdering($order);
									++$order;
									$entityManager->persist($reportVersionBlock);

									$reportModelVersion->addVersionBlock($reportVersionBlock);
								} else {
									// no system block
									$reportBlock = new ReportBlock();
									$reportBlock->setSys(0);
									$reportBlock->setName($reportModelVersionPublished->getBlock()->getName() . ' copy ' . $version->getNumberVersion());
									$entityManager->persist($reportBlock);

									$blockParams = $entityManager->getRepository(ReportBlockParam::class)->findBy(['block' => $reportModelVersionPublished->getBlock()]);

									if ($blockParams) {
										$orderParam = 1;
										foreach ($blockParams as $param) {
											$reportBlockParam = new ReportBlockParam();
											$reportBlockParam->setBlock($reportBlock);
											$reportBlockParam->setLabel($param->getLabel());
											$reportBlockParam->setOrdering($orderParam);
											++$orderParam;
											$reportBlockParam->setParam($param->getParam());
											$entityManager->persist($reportBlockParam);
										}
									}

									$reportModelVersionBlock = new ReportModelVersionBlock();
									$reportModelVersionBlock->setOrdering($order);
									++$order;
									$reportModelVersionBlock->setVersion($reportModelVersion);
									$reportModelVersionBlock->setBlock($reportBlock);
									$entityManager->persist($reportModelVersionBlock);

								}
							}
						}
					}
				}
			}
		}

		$entityManager->flush();

		return $this->redirectToRoute('admin.report.model.index');
	}

	/**
	 * @Route("/{reportModelID}/version/{reportModelVersionID}/generate", name="admin.report.model.version.generate", requirements={"reportModelID"="\d+", "reportModelVersionID"="\d+"})
	 * @ParamConverter("reportModel", options={"id"="reportModelID"})
	 * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
	 */
	public function generateWordModelVersion(ReportModel $reportModel, ReportModelVersion $reportModelVersion, WordGenerator $wordGenerator): BinaryFileResponse
	{
		$documentsReportsDir = $this->getParameter('DOCUMENTS_REPORTS_MODEL_PATH');
		$fileName            = $wordGenerator->generateVersion($reportModelVersion);

		$response = new BinaryFileResponse($documentsReportsDir . $fileName);
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

		return $response;
	}
}
