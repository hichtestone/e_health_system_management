<?php

namespace App\ESM\Controller\Admin;

use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportBlockParam;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Entity\ReportModelVersionBlock;
use App\ESM\FormHandler\ReportBlockHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/admin/report/model/version/show/{reportModelVersionID}/block")
 */
class ReportBlockController extends AbstractController
{
    /**
     * @Route("/new", name="admin.report.model.version.block.new", requirements={"reportModelVersionID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @Security("is_granted('REPORT_BLOCK_CREATE')")
     */
    public function new(ReportModelVersion $reportModelVersion, Request $request, ReportBlockHandler $reportBlockHandler): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reportBlock = new ReportBlock();

        if ($reportBlockHandler->handle($request, $reportBlock, ['reportModelVersion' => $reportModelVersion])) {
            $reportModelVersionBlock = new ReportModelVersionBlock();

            $reportModelVersionBlockEntity = $entityManager->getRepository(ReportModelVersionBlock::class)->findBy(['version' => $reportModelVersion]);
            $reportModelVersionBlock->setBlock($reportBlock);
            $ordering = $reportModelVersionBlockEntity ? count($reportModelVersionBlockEntity) + 1 : 0;
            $reportModelVersionBlock->setOrdering($ordering);
            $reportModelVersionBlock->setVersion($reportModelVersion);

            $entityManager->persist($reportModelVersionBlock);
            $entityManager->flush();

            return $this->redirectToRoute('admin.report.model.version.show', ['reportModelVersionID' => $reportModelVersion->getId()]);
        }

        return $this->render('admin/report/block/create.html.twig', [
            'form' => $reportBlockHandler->createView(),
            'action' => 'create',
            'reportModelVersion' => $reportModelVersion,
        ]);
    }

    /**
     * @Route("/{reportBlockID}/rename", name="admin.report.model.version.block.rename", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     * @Security("is_granted('REPORT_BLOCK_RENAME', reportBlock)")
     */
    public function rename(ReportModelVersion $reportModelVersion, RouterInterface $router, Request $request, ReportBlockHandler $reportBlockHandler, ReportBlock $reportBlock): Response
    {
        if ($reportBlockHandler->handle($request, $reportBlock, ['reportModelVersion' => $reportModelVersion])) {
            return $this->redirectToRoute('admin.report.model.version.show', [
                'reportModelVersionID' => $reportModelVersion->getId(),
            ]);
        }

        return $this->render('admin/report/block/create.html.twig', [
            'form' => $reportBlockHandler->createView(),
            'action' => 'rename',
            'reportModelVersion' => $reportModelVersion,
        ]);
    }

    /**
     * @Route("/{reportBlockID}/edit", name="admin.report.model.version.block.edit", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     * @Security("is_granted('REPORT_BLOCK_EDIT', reportBlock)")
     */
    public function edit(ReportBlock $reportBlock, ReportModelVersion $reportModelVersion): Response
    {
        $params = [];
        foreach ($reportBlock->getBlockParams() as $blockParam) {
            $data = json_decode($blockParam->getParam());

            if ($data) {
                if (isset($param->input) && ('text' === $param->input || 'textarea' === $param->input)) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'label' => $param->label,
                        'name' => $param->name,
                    ];
                }
                if (isset($param->input) && 'header' === $param->input) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'type' => $param->input,
                        'label' => $param->label,
                        'name' => $param->name,
                    ];
                }
                if (isset($param->input) && 'table' === $param->input) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'name' => $param->name,
                        'row' => $param->row,
                        'col' => $param->col,
                        'cells' => [
                            'cellRows' => $param->cells->cellRows,
                            'cellCols' => $param->cells->cellCols,
                        ],
                    ];
                }
            }
        }

        return $this->render('admin/report/block/edit.html.twig', [
            'reportModelVersion' => $reportModelVersion,
            'reportBlock' => $reportBlock,
            'params' => $params,
            'action' => 'edit',
        ]);
    }

    /**
     * @Route("/{reportBlockID}/delete", name="admin.report.model.version.block.delete", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     * @Security("is_granted('REPORT_BLOCK_DELETE', reportBlock)")
     */
    public function delete(ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        return $this->json([
            'html' => $this->renderView('admin/report/block/delete.html.twig', [
                'reportModelVersionID' => $reportModelVersion->getId(),
                'reportBlockID' => $reportBlock->getId(),
            ]),
        ]);
    }

    /**
     * @Route("/{reportBlockID}/delete/btn", name="admin.report.model.version.block.delete.btn", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     * @Security("is_granted('REPORT_BLOCK_DELETE', reportBlock)")
     */
    public function deleteBtn(ReportModelVersion $reportModelVersion, RouterInterface $router, Request $request, ReportBlockHandler $reportBlockHandler, ReportBlock $reportBlock): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($reportBlock);

        $entityManager->flush();

        return $this->redirectToRoute('admin.report.model.version.show', [
            'reportModelVersionID' => $reportModelVersion->getId(),
        ]);
    }

    /**
     * @Route("/{reportBlockID}/edit/param/list", name="admin.report.model.version.block.edit.param.list", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function listBlockParam(ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reportBlockParam = $entityManager->getRepository(ReportBlockParam::class)->findBy(['block' => $reportBlock], ['ordering' => 'ASC']);

        $params = [];
        foreach ($reportBlockParam as $blockParam) {
            $param = json_decode($blockParam->getParam());

            if ($param) {
                if ('text' === $param->input || 'textarea' === $param->input) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'label' => $param->label,
                        'name' => $param->name,
                        'parameterName' => $param->parameterName,
                    ];
                }
                if ('header' === $param->input) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'type' => $param->type,
                        'label' => $param->label,
                        'name' => $param->name,
                        'parameterName' => $param->parameterName,
                    ];
                }
                if ('table' === $param->input) {
                    $params[] = [
                        'id' => $blockParam->getId(),
                        'input' => $param->input,
                        'name' => $param->name,
                        'row' => $param->row,
                        'col' => $param->col,
                        'cells' => [
                            'cellRows' => $param->cells->cellRows,
                            'cellCols' => $param->cells->cellCols,
                        ],
                        'parameterName' => $param->parameterName,
                    ];
                }
            }
        }

        return $this->json($params);
    }

    /**
     * @Route("/{reportBlockID}/edit/param/new", name="admin.report.model.version.block.edit.param.new", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function newBlockParam(ReportBlock $reportBlock, Request $request): JsonResponse
    {
        $status = 0;
        $msg = 'Error';

        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);
        if ($data) {
            foreach ($data['data'] as $data) {
                $reportBlockParam = new ReportBlockParam();
                $reportBlockParam->setBlock($reportBlock);
                $reportBlockParam->setLabel($data['parameterName']);
                $reportBlockParam->setOrdering(1);
                $reportBlockParam->setParam(json_encode($data));
                $entityManager->persist($reportBlockParam);
            }
            $entityManager->flush();

            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{reportBlockID}/edit/param/edit", name="admin.report.model.version.block.edit.param.edit", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function editBlockParam(Request $request, ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        $status = 0;
        $msg = 'Error';

        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        if ($data) {
            $reportBlockParam = $entityManager->getRepository(ReportBlockParam::class)->find($data['data']['id']);
            $param = [];
            if (isset($data['data']['input']) && ('text' == $data['data']['input'] || 'textarea' == $data['data']['input'])) {
                $param = [
                    'input' => $data['data']['input'],
                    'label' => $data['data']['label'],
                    'name' => $data['data']['name'],
                    'parameterName' => $data['data']['parameterName'],
                ];
            }

            if (isset($data['data']['input']) && 'header' == $data['data']['input']) {
                $param = [
                    'input' => $data['data']['input'],
                    'type' => $data['data']['type'],
                    'label' => $data['data']['label'],
                    'name' => $data['data']['name'],
                    'parameterName' => $data['data']['parameterName'],
                ];
            }

            if (isset($data['data']['input']) && 'table' == $data['data']['input']) {
                $param = [
                    'input' => $data['data']['input'],
                    'name' => $data['data']['name'],
                    'row' => $data['data']['row'],
                    'col' => $data['data']['col'],
                    'cells' => [
                        'cellRows' => $data['data']['cells']['cellRows'],
                        'cellCols' => $data['data']['cells']['cellCols'],
                    ],
                    'parameterName' => $data['data']['parameterName'],
                ];
            }

            $reportBlockParam->setParam(json_encode($param));
            $reportBlockParam->setLabel($param['parameterName']);
            $entityManager->persist($reportBlockParam);
            $entityManager->flush();

            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{reportBlockID}/edit/param/delete", name="admin.report.model.version.block.edit.param.delete", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function deleteBlockParam(Request $request, ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        $status = 0;
        $msg = 'Error';

        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        if ($data) {
            $reportBlockParam = $entityManager->getRepository(ReportBlockParam::class)->find($data['data']['id']);

            $entityManager->remove($reportBlockParam);
            $entityManager->flush();

            $status = 1;
            $msg = 'ok';
        }

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/{reportBlockID}/edit/param/order", name="admin.report.model.version.block.edit.param.order", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function orderListBlockParam(Request $request, RouterInterface $router, ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reportBlockParams = $entityManager->getRepository(ReportBlockParam::class)->findBy(['block' => $reportBlock], ['ordering' => 'ASC']);

        return $this->json(array_map(function ($reportBlockParam) {
            return [
                'id' => $reportBlockParam->getId(),
                'ordering' => $reportBlockParam->getOrdering(),
                'label' => $reportBlockParam->getLabel(),
            ];
        }, $reportBlockParams));
    }

    /**
     * @Route("/{reportBlockID}/edit/param/ordering", name="admin.report.model.version.block.edit.param.ordering", requirements={"reportModelVersionID"="\d+", "reportBlockID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @ParamConverter("reportBlock", options={"id"="reportBlockID"})
     */
    public function orderingBlockParam(Request $request, RouterInterface $router, ReportModelVersion $reportModelVersion, ReportBlock $reportBlock): Response
    {
        $status = 0;
        $msg = 'Error';

        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        if ($data) {
            foreach ($data['data'] as $reportBlockParam) {
                $entity = $entityManager->getRepository(ReportBlockParam::class)->find($reportBlockParam['id']);
                $entity->setOrdering($reportBlockParam['ordering']);

                $entityManager->persist($entity);
                $entityManager->flush();
            }
        }

        $status = 1;
        $msg = 'Ok';

        return $this->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    /**
     * @Route("/show/order", name="admin.report.model.version.show.block.order", options={"expose"=true}, methods={"GET"}, requirements={"reportModelVersionID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @Security("is_granted('REPORT_BLOCK_ORDER')")
     */
    public function orderListBlock(ReportModelVersion $reportModelVersion): Response
    {
        $reportVersionBlocks = $reportModelVersion->getVersionBlocks()->toArray();

        return $this->json(array_map(function ($reportVersionBlock) {
            return [
                'id' => $reportVersionBlock->getId(),
                'ordering' => $reportVersionBlock->getOrdering(),
                'name' => $reportVersionBlock->getBlock()->getName(),
                'sys' => $reportVersionBlock->getBlock()->isSys(),
            ];
        }, $reportVersionBlocks));
    }

    /**
     * @Route("/show/block/ordering", name="admin.report.model.show.block.ordering", options={"expose"=true}, methods={"POST"}, requirements={"reportModelVersionID"="\d+"})
     * @ParamConverter("reportModelVersion", options={"id"="reportModelVersionID"})
     * @Security("is_granted('REPORT_BLOCK_ORDER')")
     */
    public function orderingBlock(Request $request, ReportModelVersion $reportModelVersion): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

		$messageErrors         = [];
		$messageStatus         = 'OK';

		try {
			if ($data) {
				foreach ($data['data'] as $reportBlock) {
					$entity = $entityManager->getRepository(ReportModelVersionBlock::class)->find($reportBlock['id']);
					$entity->setOrdering($reportBlock['ordering']);

					$entityManager->persist($entity);
					$entityManager->flush();
				}
			}
		} catch (\Exception $exception) {
			$messageStatus   = 'KO';
			$messageErrors[] = 'Erreur: Impossible de répositionner les blocs';
		}

		return $this->json([
			'messageStatus' => $messageStatus,
			'messageErrors' => $messageErrors,
		]);
    }
}
