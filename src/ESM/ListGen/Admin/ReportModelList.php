<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportModelList.
 */
class ReportModelList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(ReportModel::class);
        $url = 'admin.report.model.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setId('reportModel-list-table')

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
            ->setExportFileName('reportModel')

            ->setRepositoryMethod('indexListGen', [])
            ->addConstantSort('reportModel.name', 'ASC')
        ;

        $list

            ->addColumn([
                'label' => '',
                'sortable' => false,
                'formatter' => function ($row) {
                    return '<input type="checkbox"></input>';
                },
            ])

            ->addColumn([
                'label' => 'entity.report_model.field.report_type.name',
                'formatter' => function ($row) use ($translator) {
                    return $translator->trans(ReportModel::REPORT_TYPE[$row['report_model']->getReportType()]) ?? '';
                },
                'formatter_csv' => function ($row) use ($translator) {
                    return $translator->trans(ReportModel::REPORT_TYPE[$row['report_model']->getReportType()]) ?? '';
                },
                'sortField' => 'reportModel.reportType',
            ])

            ->addColumn([
                'label' => 'entity.report_model.field.visit_type.name',
                'formatter' => function ($row) use ($translator) {
                    return $translator->trans(ReportModel::VISIT_TYPE[$row['report_model']->getVisitType()]) ?? '';
                },
                'formatter_csv' => function ($row) use ($translator) {
                    return $translator->trans(ReportModel::VISIT_TYPE[$row['report_model']->getVisitType()]) ?? '';
                },
                'sortField' => 'reportModel.visitType',
            ])
            ->addColumn([
                'label' => 'entity.report_model.field.name',
                'field' => 'reportModel.name',
            ])
            ->addColumn([
                'label' => 'entity.report_model_version.field.status.create',
                'formatter' => function ($row) {
                    if (count($row['report_model']->getVersions()) > 0) {
                        foreach ($row['report_model']->getVersions() as $version) {
                            if (($version->getReportModel()->getId() === $row['report_model']->getId()) && (ReportModelVersion::STATUS_CREATE === $version->getStatus())) {
                                return 'Version '.$version->getNumber();
                            } else {
                                return '';
                            }
                        }
                    } else {
                        return '';
                    }
                },
                'formatter_csv' => function ($row) {
                    if (count($row['report_model']->getVersions()) > 0) {
                        foreach ($row['report_model']->getVersions() as $version) {
                            if (($version->getReportModel()->getId() === $row['report_model']->getId()) && (ReportModelVersion::STATUS_CREATE === $version->getStatus())) {
                                return 'Version '.$version->getNumber();
                            } else {
                                return '';
                            }
                        }
                    } else {
                        return '';
                    }
                },
                'sortField' => 'versions.number',
            ])
           ->addColumn([
               'label' => 'entity.report_model_version.field.status.publish',
               'formatter' => function ($row) {
                   if (count($row['report_model']->getVersions()) > 0) {
                       foreach ($row['report_model']->getVersions() as $version) {
                           if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                               return 'Version '.$version->getNumber();
                           } else {
                               return '';
                           }
                       }
                   } else {
                       return '';
                   }
               },
               'formatter_csv' => function ($row) {
                   if (count($row['report_model']->getVersions()) > 0) {
                       foreach ($row['report_model']->getVersions() as $version) {
                           if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                               return 'Version '.$version->getNumber();
                           } else {
                               return '';
                           }
                       }
                   } else {
                       return '';
                   }
               },
               'sortField' => 'versions.number',
           ])
            ->addColumn([
                'label' => 'entity.report_model_version.field.status.publishedAt',
                'formatter' => function ($row) {
                    if (count($row['report_model']->getVersions()) > 0) {
                        foreach ($row['report_model']->getVersions() as $version) {
                            if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                                return $version->getPublishedAt()->format('d/m/Y') ?? '';
                            } else {
                                return '';
                            }
                        }
                    } else {
                        return '';
                    }
                },
                'sortField' => 'versions.publishedAt',
                'formatter_csv' => function ($row) {
                    foreach ($row['report_model']->getVersions() as $version) {
                        if ($version->getReportModel()->getId() == $row['report_model']->getId() && (null != $version->getCreatedAt() && null != $version->getPublishedAt())) {
                            return $version->getPublishedAt()->format('d/m/Y') ?? '';
                        } else {
                            return '';
                        }
                    }
                },
            ])
            ->addAction([
                'href' => function ($row) {
                    if ($this->security->isGranted('REPORT_MODEL_SHOW', $row['report_model'])) {
                        return $this->router->generate('admin.report.model.version.show', ['reportModelID' => $row['report_model']->getId()]);
                    } else {
                        return '';
                    }
                },
                'formatter' => function ($row) {
                    if ($this->security->isGranted('REPORT_MODEL_SHOW', $row['report_model'])) {
                        return '<i class="fa fa-eye c-grey"></i>';
                    } else {
                        return '';
                    }
                },
            ])
            ->addAction([
                'href' => function ($row) {
                    if ($this->security->isGranted('REPORT_MODEL_CREATE_VERSION', $row['report_model'])) {
                        return $this->router->generate('admin.report.model.version.new', ['reportModelID' => $row['report_model']->getId()]);
                    } else {
                        return '';
                    }
                },
                'formatter' => function ($row) use ($translator) {
                    if ($this->security->isGranted('REPORT_MODEL_CREATE_VERSION', $row['report_model'])) {
                        return '<i class="fa fa-plus-square c-grey"></i>';
                    } else {
                        return '<i class="fa fa-plus-square c-grey disabled" href data-placement="right" data-toggle="tooltip" title="'.$translator->trans('entity.report_model.action.new_message').'"></i>';
                    }
                },
            ])
            ->addAction([
                'href' => function ($row) {
                    if ($this->security->isGranted('REPORT_MODEL_DELETE', $row['report_model'])) {
                        return $this->router->generate('admin.report.model.delete', ['reportModelID' => $row['report_model']->getId()]);
                    } else {
                        return '';
                    }
                },
                'formatter' => function ($row) use ($translator) {
                    if ($this->security->isGranted('REPORT_MODEL_DELETE', $row['report_model'])) {
                        return '<i class="fa fa-trash c-grey"></i>';
                    } else {
                        return '<i class="fa fa-trash c-grey disabled" href data-placement="right" data-toggle="tooltip" title="'.$translator->trans('entity.report_model.action.delete_message').'"></i>';
                    }
                },
            ])
            ->addFilter([
                'label' => 'entity.report_model.field.report_type.name',
                'field' => 'reportModel.reportType',
                'selectLabel' => function ($item) use ($translator) {
                    switch ($item) {
                        case ReportModel::REPORT_IN_FOLLOW_UP:
                            return $translator->trans(ReportModel::REPORT_TYPE[ReportModel::REPORT_IN_FOLLOW_UP]);
                        case ReportModel::REPORT_PROD:
                            return $translator->trans(ReportModel::REPORT_TYPE[ReportModel::REPORT_PROD]);
                        case ReportModel::REPORT_CLOS:
                            return $translator->trans(ReportModel::REPORT_TYPE[ReportModel::REPORT_CLOS]);
                    }
                },
            ], 'select')
            ->addFilter([
                'label' => 'entity.report_model.field.visit_type.name',
                'field' => 'reportModel.visitType',
                'selectLabel' => function ($item) use ($translator) {
                    switch ($item) {
                        case ReportModel::VISIT_ON_SITE:
                            return $translator->trans(ReportModel::VISIT_TYPE[ReportModel::VISIT_ON_SITE]);
                        case ReportModel::VISIT_OFF_SITE:
                            return $translator->trans(ReportModel::VISIT_TYPE[ReportModel::VISIT_OFF_SITE]);
                    }
                },
            ], 'select')
            ->addFilter([
                'label' => 'entity.report_model.field.name',
                'field' => 'reportModel.name',
                'selectLabel' => 'reportModel.name',
            ], 'select')
        ;

        return $list;
    }
}
