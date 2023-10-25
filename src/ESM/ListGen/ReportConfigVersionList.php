<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportConfigVersionList.
 */
class ReportConfigVersionList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(Project $project, TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(ReportConfigVersion::class);
		$url 		= 'project.settings.report.config.version.index.ajax';
		$urlArgs 	= ['projectID' => $project->getId()];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('reportConfigVersion-list-table')
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'reportConfigVersion',
				'alias' => 'report_config_version',
			])
			->addHiddenData([
				'field' => 'reportConfigVersion.id',
			])
			->setRepository($repository)
			->setExportFileName('reportConfigVersion')
			->setRepositoryMethod('indexListGen', [$project])
		;

		$list
			->addColumn([
				'label' => 'entity.report_model.field.report_type.name',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::REPORT_TYPE[$row['report_config_version']->getModelType()]) ?? '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::REPORT_TYPE[$row['report_config_version']->getModelType()]) ?? '';
				},
				'sortField' => 'model.reportType',
			])
			->addColumn([
				'label' => 'entity.report_model.field.visit_type.name',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::VISIT_TYPE[$row['report_config_version']->getModelTypeVisit()]) ?? '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModel::VISIT_TYPE[$row['report_config_version']->getModelTypeVisit()]) ?? '';
				},
				'sortField' => 'model.visitType',
			])
			->addColumn([
				'label' => 'entity.report_model.field.name',
				'formatter' => function ($row) {
					return $row['report_config_version']->getModelName();
				},
				'formatter_csv' => function ($row) {
					return $row['report_config_version']->getModelName();
				},
				'sortField' => 'model.name',
			])
			->addColumn([
				'label' => 'entity.report_model.field.published_version',
				'formatter' => function ($row) {
					return 'Version '.$row['report_config_version']->getModelPublishedVersion();
				},
				'formatter_csv' => function ($row) {
					return 'Version '.$row['report_config_version']->getModelPublishedVersion();
				},
				'sortField' => 'modelVersion.numberVersion',
			])
			->addColumn([
				'label' => 'entity.report_model.field.publishedAt',
				'formatter' => function ($row) {
					$date = $row['report_config_version']->getModelPublishedAt();

					return null != $date ? $date->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					$date = $row['report_config_version']->getModelPublishedAt();

					return null != $date ? $date->format('d/m/Y') : '';
				},
				'sortField' => 'modelVersion.publishedAt',
			])
			->addColumn([
				'label' => 'entity.report_model.field.status',
				'sortField' => 'versions.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(ReportModelVersion::STATUS[$row['report_config_version']->getModelStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(ReportModelVersion::STATUS[$row['report_config_version']->getModelStatus()]);
				},
			])
			->addColumn([
				'label' => 'entity.report_model.field.activated',
				'formatter' => function ($row) {
					switch ($row['report_config_version']->getStatus()) {
						case 0:
							return '<i class="fa fa-times c-red"></i>';
						case 1:
							return '<i class="fa fa-check c-green"></i>';
						case 2:
							return '<i class="fa fa-times c-grey"></i>';
					}
				},
				'formatter_csv' => function ($row) {
					switch ($row['report_config_version']->getStatus()) {
						case 0:
							return ReportConfigVersion::STATUS_INACTIVE;
						case 1:
							return ReportConfigVersion::STATUS_ACTIVE;
						case 2:
							return ReportConfigVersion::STATUS_OUTDATED;
					}
				},
				'sortField' => 'modelVersion.status',
			])
			->addColumn([
				'label' => 'entity.report_model.field.config_version',
				'formatter' => function ($row) {
					return $row['report_config_version']->getNumberVersion();
				},
				'formatter_csv' => function ($row) {
					return $row['report_config_version']->getNumberVersion();
				},
				'sortField' => 'reportConfigVersion.numberVersion',
			])
			->addAction([
				'href' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version'])) {
						return $this->router->generate('project.settings.report.config.version.show', ['projectID' => $project->getId(), 'reportConfigID' => $row['report_config_version']->getConfig()->getId(), 'reportConfigVersionID' => $row['report_config_version']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version'])) {
						return '<i class="fa fa-eye c-grey"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						if ($row['report_config_version']->getModelStatus() === ReportModelVersion::STATUS_OBSOLETE) {
							return '';

						} else {
							if ($row['report_config_version']->getStatus() === ReportConfigVersion::STATUS_ACTIVE) {
								return $this->router->generate('project.settings.report.config.version.deactivate', ['projectID' => $project->getId(), 'reportConfigID' => $row['report_config_version']->getConfig()->getId(), 'reportConfigVersionID' => $row['report_config_version']->getId()]);
							} elseif ($row['report_config_version']->getStatus() === ReportConfigVersion::STATUS_INACTIVE) {
								return $this->router->generate('project.settings.report.config.version.activate', ['projectID' => $project->getId(), 'reportConfigID' => $row['report_config_version']->getConfig()->getId(), 'reportConfigVersionID' => $row['report_config_version']->getId()]);
							} else {
								return '';
							}
						}
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project, $translator) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						if ($row['report_config_version']->getModelStatus() === ReportModelVersion::STATUS_OBSOLETE) {
							return '<i class="fa fa-info-circle c-grey" href data-placement="right" data-toggle="tooltip" title="' . $translator->trans('entity.report_model.action.obsolete_message') . '"></i>';
						} else {
							if ($row['report_config_version']->getStatus() === ReportConfigVersion::STATUS_ACTIVE) {
								return '<i class="fa fa-times c-red"></i>';
							} elseif ($row['report_config_version']->getStatus() === ReportConfigVersion::STATUS_INACTIVE) {
								return '<i class="fa fa-info-circle c-green"></i>';
							} else {
								return '';
							}
						}
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('admin.report.config.version.generate', ['projectID' => $project->getId(), 'reportConfigVersionID' => $row['report_config_version']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_CONFIG_SHOW', $row['report_config_version']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-file-word"></i>';
					} else {
						return '';
					}
				},
			])
			->addFilter([
				'label' => 'entity.report_model.field.report_type.name',
				'field' => 'model.reportType',
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
				'field' => 'model.visitType',
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
				'field' => 'model.name',
				'selectLabel' => 'model.name',
			], 'select')
		;

		return $list;
	}
}
