<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\Visit;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class VisitSettingList.
 */
class VisitSettingList extends AbstractListGenType
{
	/**
	 * @return mixed
	 *
	 * @throws InvalidParameterException
	 * @throws MissingMandatoryParametersException
	 * @throws RouteNotFoundException
	 */
	public function getList(Project $project, TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(Visit::class);
		$url        = 'project.visit.setting.index.ajax';
		$urlArgs    = ['id' => $project->getId()];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'v',
				'alias' => 'visit',
			])
			->addHiddenData([
				'field' => 'v.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [$project])
			->setExportFileName('visit-settings')
			->addConstantSort('v.position', 'ASC');

		$list
			->addColumn([
				'label'         => 'entity.VisitSetting.field.phase',
				'formatter'     => function ($row) use ($project) {
					return $row['visit']->getPhase()->getLabel();
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['visit']->getPhase()->getLabel();
				},
				'sortField'     => 'v.phase',
			])
			->addColumn([
				'label' => 'entity.VisitSetting.field.short',
				'field' => 'v.short',
			])
			->addColumn([
				'label'         => 'entity.VisitSetting.field.label',
				'formatter'     => function ($row) use ($project) {
					return $row['visit']->getLabel() ? $row['visit']->getLabel() : '';
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['visit']->getLabel() ? $row['visit']->getLabel() : '';
				},
				'sortField'     => 'v.label',
			])
			->addColumn([
				'label'         => 'entity.VisitSetting.field.price',
				'formatter'     => function ($row) use ($project) {
					return $row['visit']->getPrice() . '&euro;';
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['visit']->getPrice() . '€';
				},
				'sortField'     => 'v.price',
			])
			->addColumn([
				'label'         => 'entity.VisitSetting.field.delay',
				'formatter'     => function ($row) use ($project) {
					return ($row['visit']->getDelay() !== null) ? $row['visit']->getDelay() . ' +/- ' . $row['visit']->getDelayApprox() . ' jour(s)' : '';
				},
				'formatter_csv' => function ($row) use ($project) {
					return ($row['visit']->getDelay() !== null) ? $row['visit']->getDelay() . ' +/- ' . $row['visit']->getDelayApprox() . ' jour(s)' : '';
				},
				'sortField'     => 'v.delay',
			])
			->addColumn([
				'label'         => 'entity.VisitSetting.field.patientVariable',
				'formatter'     => function ($row) use ($project) {
					return $row['visit']->getPatientVariable() ? $row['visit']->getPatientVariable()->getLabel() : '';
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['visit']->getPatientVariable() ? $row['visit']->getPatientVariable()->getLabel() : '';
				},
				'sortField'     => 'v.label',
			])
			->addFilter([
				'label'       => 'entity.VisitSetting.field.phase',
				'field'       => 'ph.label',
				'selectLabel' => 'ph.label',
			], 'select')
			->addFilter([
				'label'       => 'entity.VisitSetting.field.short',
				'field'       => 'v.id',
				'selectLabel' => 'v.short',
			], 'select');

		$list->addAction([
			'href'      => function ($row) use ($project) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.visit.setting.edit', ['id' => $project->getId(), 'idVisit' => $row['visit']->getId()]);
				} else {
					return '';
				}
			},
			'formatter' => function ($row) use ($translator, $project) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
                   return '<i class="fa fa-edit" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.VisitSetting.messageUpdated') . '"></i>';
                } else {
					return '';
				}

			},
		]);

		$list->addAction([
			'href'      => function ($row) use ($project) {

				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					if (null === $row['visit']->getDeletedAt()) {
						if ($this->security->isGranted('VISITSETTING_ARCHIVE', $row['visit'])) {
							return $this->router->generate('project.visit.setting.archive', ['id' => $project->getId(), 'idVisit' => $row['visit']->getId()]);
						} else {
							return '';
						}
					} else {
						return $this->router->generate('project.visit.setting.restore', ['id' => $project->getId(), 'idVisit' => $row['visit']->getId()]);
					}
				} else {
					return '';
				}
			},
			'formatter' => function ($row) use ($project, $translator) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					if (null == $row['visit']->getDeletedAt()) {
						if ($this->security->isGranted('VISITSETTING_ARCHIVE', $row['visit'])) {
							return '<i class="fa fa-archive"></i>';
						} else {
							return '<i class="fa fa-archive disabled" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.VisitSetting.messageArchived') . '"></i>';
						}
					} else {
						return '<i class="fa fa-box-open"></i>';
					}
				} else {
					return '';
				}
			},
		]);

		$list->addAction([
			'href'      => function ($row) use ($project) {

				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.visit.setting.clone', ['id' => $project->getId(), 'idVisit' => $row['visit']->getId()]);
				} else {
					return '';
				}
			},
			'formatter' => function ($row) use ($project, $translator) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
                    return '<i class="fa fa-clone" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.VisitSetting.messageDuplicated') . '"></i>';
				} else {
					return '';
				}
			},
		])

			->addColumn([
				'label'         => 'entity.status.label',
				'formatter'     => function ($row) {
					return null === $row['visit']->getDeletedAt() ? 'Non archivé' : 'archivé';
				},
				'formatter_csv' => 'formatter',
				'sortField'     => 'v.deletedAt',
			]);

			if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')) {
				$list->addFilter([
					'label'              => 'filter.archived.label',
					'translation_domain' => 'ListGen',
					'field'              => 'v.deletedAt',
					'defaultValues'      => [0],
				], 'archived');
			}

		return $list;
	}
}
