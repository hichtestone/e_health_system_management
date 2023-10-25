<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Center;
use App\ESM\Entity\Project;
use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportVisit;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportVisitList.
 */
class ReportVisitList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(Project $project, Center $center, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(ReportVisit::class);
        $url = 'project.center.report.visit.center.index.ajax';
        $urlArgs = ['projectID' => $project->getId(), 'centerID' => $center->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setId('reportVisit')
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'reportVisit',
                'alias' => 'report_visit',
            ])
            ->addHiddenData([
                'field' => 'reportVisit.id',
            ])

            ->setRepository($repository)
            ->setExportFileName('reportModel')

            ->setRepositoryMethod('indexListGen', [$project, $center])
            ->addConstantSort('reportVisit.numberVisit', 'ASC')
        ;

        $list
            ->addColumn([
                'label' => 'entity.report_visit.field.number',
                'field' => 'reportVisit.numberVisit',
            ])
           ->addColumn([
                'label' => 'entity.report_visit.field.expectedAt',
                'formatter' => function ($row) {
                    return (null === $row['report_visit']->getExpectedAt()) ? '' : $row['report_visit']->getExpectedAt()->format('d/m/Y');
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'reportVisit.expectedAt',
            ])
			->addColumn([
			   'label' => 'entity.report_visit.field.createdAt',
			   'formatter' => function ($row) {
				   return (null === $row['report_visit']->getReportedAt()) ? '' : $row['report_visit']->getReportedAt()->format('d/m/Y');
			   },
			   'formatter_csv' => function ($row) {
				   return (null === $row['report_visit']->getReportedAt()) ? '' : $row['report_visit']->getReportedAt()->format('d/m/Y');
			   },
			   'sortField' => 'reportVisit.createdAt',
		   ])
			->addColumn([
				'label' => 'entity.report_visit.field.visit_status.name',
				'formatter' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getVisitStatus()) ? '' : $translator->trans(ReportVisit::VISIT_STATUS[$row['report_visit']->getVisitStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getVisitStatus()) ? '' : $translator->trans(ReportVisit::VISIT_STATUS[$row['report_visit']->getVisitStatus()]);
				},
				'sortField' => 'reportVisit.visitStatus',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.visit_type.name',
				'formatter' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getVisitType()) ? '' : $translator->trans(ReportVisit::VISIT_TYPE[$row['report_visit']->getVisitType()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getVisitType()) ? '' : $translator->trans(ReportVisit::VISIT_TYPE[$row['report_visit']->getVisitType()]);
				},
				'sortField' => 'reportVisit.visitType',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.report_type.name',
				'formatter' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getModelReportType()) ? '' : $translator->trans(ReportModel::REPORT_TYPE[$row['report_visit']->getModelReportType()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getModelReportType()) ? '' : $translator->trans(ReportModel::REPORT_TYPE[$row['report_visit']->getModelReportType()]);
				},
				'sortField' => 'reportModel.reportType',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.code',
				'formatter' => function ($row) use ($translator) {
					return $row['report_visit']->getCode() ?? '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $row['report_visit']->getCode() ?? '';
				},
				'sortField' => 'reportVisit.code',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.createdBy',
				'formatter' => function ($row) {
					return (string) $row['report_visit']->getReportedBy();
				},
				'formatter_csv' => function ($row) {
					return (string) $row['report_visit']->getReportedBy();
				},
				'sortField' => 'reportVisit.createdBy',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.validatedBy',
				'formatter' => function ($row) {
					return (string) $row['report_visit']->getValidatedBy();
				},
				'formatter_csv' => function ($row) {
					return (string) $row['report_visit']->getValidatedBy();
				},
				'sortField' => 'reportVisit.validatedBy',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.report_status.name',
				'formatter' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getReportStatus()) ? '' : $translator->trans(ReportVisit::REPORT_STATUS[$row['report_visit']->getReportStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return (null === $row['report_visit']->getReportStatus()) ? '' : $translator->trans(ReportVisit::REPORT_STATUS[$row['report_visit']->getReportStatus()]);
				},
				'sortField' => 'reportVisit.report_status',
			])
			->addColumn([
				'label' => 'entity.report_visit.field.validatedAt',
				'formatter' => function ($row) {
					return (null === $row['report_visit']->getValidatedAt()) ? '' : $row['report_visit']->getValidatedAt()->format('d/m/Y');
				},
				'formatter_csv' => function ($row) {
					return (null === $row['report_visit']->getValidatedAt()) ? '' : $row['report_visit']->getValidatedAt()->format('d/m/Y');
				},
				'sortField' => 'reportVisit.validatedAt',
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DOWNLOAD', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.download.template', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DOWNLOAD', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-file-word mr-1"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DELETE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.delete', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DELETE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-trash c-red mr-1"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_VALIDATE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.validate', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_VALIDATE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-upload c-green"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($center, $project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DOWNLOAD_REPORT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.download.report', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_DOWNLOAD_REPORT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-file-pdf c-red"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_CREATE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.report.new', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_CREATE', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-file-medical"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_NO_VISIT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.noVisit', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_REPORT_NO_VISIT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-calendar-times c-red"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) use ($project, $center) {
					if ($this->security->isGranted('REPORT_VISIT_EDIT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.center.report.visit.center.edit', ['projectID' => $project->getId(), 'centerID' => $center->getId(), 'reportVisitID' => $row['report_visit']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('REPORT_VISIT_EDIT', $row['report_visit']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-edit"></i>';
					} else {
						return '';
					}
				},
			])
            ->addFilter([
                'label' => 'entity.report_visit.field.number',
                'field' => 'reportVisit.numberVisit',
                'selectLabel' => 'reportVisit.numberVisit',
            ], 'select')
			->addFilter([
				'label' => 'entity.report_visit.field.visit_status.name',
				'field' => 'reportVisit.visitStatus',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case ReportVisit::VISIT_STATUS_DONE:
							return $translator->trans(ReportVisit::VISIT_STATUS[ReportVisit::VISIT_STATUS_DONE]);
						case ReportVisit::VISIT_STATUS_NO_DONE:
							return $translator->trans(ReportVisit::VISIT_STATUS[ReportVisit::VISIT_STATUS_NO_DONE]);
					}
				},
			], 'select')
			->addFilter([
				'label' => 'entity.report_visit.field.visit_type.name',
				'field' => 'reportVisit.visitType',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case ReportVisit::VISIT_ON_SITE:
							return $translator->trans(ReportVisit::VISIT_TYPE[ReportVisit::VISIT_ON_SITE]);
						case ReportVisit::VISIT_OFF_SITE:
							return $translator->trans(ReportVisit::VISIT_TYPE[ReportVisit::VISIT_OFF_SITE]);
					}
				},
			], 'select')
			->addFilter([
				'label' => 'entity.report_visit.field.report_type.name',
				'field' => 'reportVisit.reportType',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case ReportVisit::REPORT_IN_FOLLOW_UP:
							return $translator->trans(ReportVisit::REPORT_TYPE[ReportVisit::REPORT_IN_FOLLOW_UP]);
						case ReportVisit::REPORT_PROD:
							return $translator->trans(ReportVisit::REPORT_TYPE[ReportVisit::REPORT_PROD]);
						case ReportVisit::REPORT_CLOS:
							return $translator->trans(ReportVisit::REPORT_TYPE[ReportVisit::REPORT_CLOS]);
					}
				},
			], 'select')
        ;

        return $list;
    }
}
