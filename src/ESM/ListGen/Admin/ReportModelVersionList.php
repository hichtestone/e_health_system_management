<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportModelVersionList.
 */
class ReportModelVersionList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(ReportModelVersion::class);
		$url        = 'admin.report.model.version.index.ajax';
		$urlArgs    = [];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('reportModelVersion-list-table')
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'reportModelVersion',
				'alias' => 'report_model_version',
			])
			->addHiddenData([
				'field' => 'reportModelVersion.id',
			])
			->setRepository($repository)
			->setExportFileName('reportModelVersion')
			->setRepositoryMethod('indexListGen', []);

		$list
			->addColumn([
				'label' => '',
				'sortable' => true,
				'formatter' => function ($row) {
					return '<input type="checkbox"/>';
				},
				'formatter_csv' => function ($row) {
					return '';
				},
				'sortField' => 'reportModelVersion.id',
			])
			->addColumn([
				'label' => 'entity.report_model.field.name',
				'formatter' => function ($row) {
					return '<a href="'.$this->router->generate('admin.report.model.version.show', ['reportModelVersionID' => $row['report_model_version']->getId()]).'">'.
						$row['report_model_version']->getReportModel()->getName().'</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['report_model_version']->getReportModel()->getName();
				},
				'sortable' => true,
				'sortField' => 'reportModel.name',
			])
			->addColumn([
				'label' => 'entity.report_model.field.report_type.name',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::REPORT_TYPE[$row['report_model_version']->getReportModel()->getReportType()]) ?? '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::REPORT_TYPE[$row['report_model_version']->getReportModel()->getReportType()]) ?? '';
				},
				'sortField' => 'reportModel.reportType',
				'sortable' => true,
			])
			->addColumn([
				'label' => 'entity.report_model.field.visit_type.name',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::VISIT_TYPE[$row['report_model_version']->getReportModel()->getVisitType()]) ?? '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::VISIT_TYPE[$row['report_model_version']->getReportModel()->getVisitType()]) ?? '';
				},
				'sortField' => 'reportModel.visitType',
				'sortable' => true,
			])
			->addColumn([
				'label' => 'entity.report_model.field.status',
				'sortable' => true,
				'sortField' => 'reportModelVersion.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModelVersion::STATUS[$row['report_model_version']->getStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModelVersion::STATUS[$row['report_model_version']->getStatus()]);
				},
			])
			->addColumn([
				'label' => 'entity.report_model.field.version',
				'sortable' => true,
				'sortField' => 'reportModelVersion.numberVersion',
				'formatter' => function ($row) {
					return $row['report_model_version']->getNumberVersion();
				},
				'formatter_csv' => function ($row) {
					return $row['report_model_version']->getNumberVersion();
				},
			])
			->addColumn([
				'label' => 'entity.report_model_version.field.status.publishedAt',
				'formatter' => function ($row) {
					if ($row['report_model_version']->getPublishedAt()) {
						return $row['report_model_version']->getPublishedAt()->format('d/m/Y') ?? '';
					} else {
						return '';
					}
				},
				'formatter_csv' => function ($row) {
					if ($row['report_model_version']->getPublishedAt()) {
						return $row['report_model_version']->getPublishedAt()->format('d/m/Y') ?? '';
					} else {
						return '';
					}
				},
				'sortField' => 'reportModelVersion.publishedAt',
				'sortable' => true,
			])
			->addAction([
				'href' => function ($row) {
					if ($this->security->isGranted('REPORT_MODEL_VERSION_SHOW', $row['report_model_version'])) {
						return $this->router->generate('admin.report.model.version.show', ['reportModelVersionID' => $row['report_model_version']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($translator) {
					if ($this->security->isGranted('REPORT_MODEL_VERSION_SHOW', $row['report_model_version'])) {
						return '<i class="fa fa-eye c-grey" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('entity.report_model.action.file') . '"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) {
					if ($this->security->isGranted('REPORT_MODEL_CREATE_VERSION', $row['report_model_version']->getReportModel())) {
						if ($row['report_model_version']->getReportModel()->canCreateVersion()) {
							return $this->router->generate('admin.report.model.version.new', ['reportModelID' => $row['report_model_version']->getReportModel()->getId()]);
						} else {
							return '';
						}
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($translator) {
					if ($this->security->isGranted('REPORT_MODEL_CREATE_VERSION', $row['report_model_version']->getReportModel())) {
						if ($row['report_model_version']->getReportModel()->canCreateVersion()) {
							return '<i class="fa fa-plus-square c-grey" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('entity.report_model.action.new_version') . '"></i>';
						} else {
							return '<i class="fa fa-plus-square c-grey disabled" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('entity.report_model.action.new_message') . '"></i>';
						}
					} else {
						return '<i class="fa fa-plus-square c-grey disabled" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('general.impossible') . '"></i>';
					}
				},
			])
			->addAction([
				'href' => function ($row) {
					if ($this->security->isGranted('REPORT_MODEL_DELETE', $row['report_model_version']->getReportModel())) {
						if (!$row['report_model_version']->getReportModel()->hasPublishedVersion()) {
							return $this->router->generate('admin.report.model.delete', ['reportModelID' => $row['report_model_version']->getReportModel()->getId()]);
						} else {
							return '';
						}
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($translator) {
					if ($this->security->isGranted('REPORT_MODEL_DELETE', $row['report_model_version']->getReportModel())) {
						if (!$row['report_model_version']->getReportModel()->hasPublishedVersion()) {
							return '<i class="fa fa-trash c-grey"></i>';
						} else {
							return '<i class="fa fa-trash c-grey disabled" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('entity.report_model.action.delete_message') . '"></i>';
						}
					} else {
						return '<i class="fa fa-trash c-grey disabled" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('general.impossible') . '"></i>';
					}
				},
			])
			->addFilter([
				'label' => 'entity.report_model.field.name',
				'field' => 'reportModel.name',
				'selectLabel' => 'reportModel.name',
			], 'select')
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
				'label' => 'entity.report_model.field.status',
				'field' => 'reportModelVersion.status',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case ReportModelVersion::STATUS_CREATE:
							return $translator->trans(ReportModelVersion::STATUS[ReportModelVersion::STATUS_CREATE]);
						case ReportModelVersion::STATUS_PUBLISH:
							return $translator->trans(ReportModelVersion::STATUS[ReportModelVersion::STATUS_PUBLISH]);
						case ReportModelVersion::STATUS_OBSOLETE:
							return $translator->trans(ReportModelVersion::STATUS[ReportModelVersion::STATUS_OBSOLETE]);
					}
				},
			], 'select');

		return $list;
	}
}
