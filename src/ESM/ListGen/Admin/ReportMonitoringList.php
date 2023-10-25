<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Project;
use App\ESM\Entity\ReportModel;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportMonitoringList.
 */
class ReportMonitoringList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(Project $project, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(ReportModel::class);
        $url = 'project.report.model.index.ajax';
        $urlArgs = ['id' => $project->getId()]; // Parametres de la method dans repository

        return $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                        ->setClass('table')
                        ->setRowData(['id' => 'id'])
                        ->addHiddenData([
                            'field' => 'reportModel',
                            'alias' => 'report_model',
                        ])
                        ->addHiddenData([
                            'field' => 'reportModel.id',
                        ])
                        ->setRepository($repository)
                        ->setRepositoryMethod('indexSettingListGen', [])
                        ->setExportFileName('report_model')
                        ->addConstantSort('reportModel.name', 'ASC')
                        ->addColumn([
                            'label' => 'entity.report_model.field.report_type.name',
                            'translation_args' => ['%count%' => 1],
                            'formatter' => function ($row) use ($translator) {
                                return $translator->trans(ReportModel::REPORT_TYPE[$row['report_model']->getReportType()]);
                            },
                            'formatter_csv' => 'formatter',
                            'sortField' => 'reportModel.reportType',
                        ])
                        ->addColumn([
                            'label' => 'entity.report_model.field.visit_type.name',
                            'translation_args' => ['%count%' => 1],
                            'formatter' => function ($row) use ($translator) {
                                return $translator->trans(ReportModel::VISIT_TYPE[$row['report_model']->getVisitType()]);
                            },
                            'formatter_csv' => 'formatter',
                            'sortField' => 'reportModel.visitType',
                        ])
                        ->addColumn([
                            'label' => 'entity.report_model.field.name',
                            'field' => 'reportModel.name',
                        ])
                        ->addColumn([
                            'label' => 'version publiée',
                            'translation_args' => ['%count%' => 1],
                            'formatter' => function ($row) {
                                foreach ($row['report_model']->getVersions() as $version) {
                                    if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                                        return 'Version '.$version->getNumber();
                                    } else {
                                        return '';
                                    }
                                }
                            },
                            'formatter_csv' => 'formatter',
                            'sortField' => 'versions.number',
                        ])
                        ->addColumn([
                            'label' => 'entity.report_model_version.field.status.publishedAt',
                            'translation_args' => ['%count%' => 1],
                            'formatter' => function ($row) {
                                foreach ($row['report_model']->getVersions() as $version) {
                                    if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                                        return $version->getPublishedAt()->format('d/m/Y') ?? '';
                                    } else {
                                        return '';
                                    }
                                }
                            },
                            'formatter_csv' => 'formatter',
                            'sortField' => 'versions.publishedAt',
                        ])
                        ->addColumn([
                            'label' => 'activité',
                            'formatter' => function ($row) {
                                foreach ($row['report_model']->getVersions() as $version) {
                                    if (1 == $version->getStatus()) {
                                        return '<i title="Disponible à la saisie" class="fa fa-check c-green"></i>';
                                    } elseif (0 == $version->getStatus()) {
                                        return '<i title="Indisponible à la saisie" class="fa fa-times c-red"></i>';
                                    } else {
                                        return '<i title="Indisponible Modèle obsolète sans nouvelle version publiée." class="fa fa-exclamation-circle c-grey"></i>';
                                    }
                                }
                            },
                            'formatter_csv' => 'formatter',
                            'sortField' => 'versions.status',
                        ])
                        ->addAction([
                            'href' => function ($row) use ($project) {
                                return $this->router->generate('project.setting.monitoring_model.fiche', ['id' => $project->getId(), 'idMonitoringModel' => $row['report_model']->getId()]);
                            },
                            'formatter' => function () {
                                return '<i class="fa fa-eye c-grey"></i>';
                            },
                        ])
                        ;
    }
}
