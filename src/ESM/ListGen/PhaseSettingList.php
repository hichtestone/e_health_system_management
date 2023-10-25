<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhaseSettingList.
 */
class PhaseSettingList extends AbstractListGenType
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
		$repository = $this->em->getRepository(PhaseSetting::class);
		$url        = 'project.phase.setting.index.ajax';
		$urlArgs    = ['id' => $project->getId()];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'p',
				'alias' => 'phase',
			])
			->addHiddenData([
				'field' => 'p.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [$project])
			->setExportFileName('phase-settings')
			->addConstantSort('p.position', 'ASC');

		$list
			->addColumn([
				'label' => 'entity.PhaseSetting.field.order',
				'field' => 'p.ordre',
			])
			->addColumn([
				'label' => 'entity.PhaseSetting.field.label',
				'field' => 'p.label',
			])
			->addColumn([
				'label' => 'entity.PhaseSetting.field.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans($row['phase']->getPhaseSettingStatus()->getLabel());
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans($row['phase']->getPhaseSettingStatus()->getLabel());
				},
				'sortField' => 'p.phaseSettingStatus'
			])
			->addColumn([
				'label'         => 'entity.status.archived',
				'formatter'     => function ($row) {
					return null === $row['phase']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => 'formatter',
				'sortField'     => 'p.deletedAt',
			])
			->addFilter([
				'label'              => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field'              => 'p.deletedAt',
				'defaultValues'      => [0],
			], 'archived')
			->addFilter([
				'label'       => 'entity.PhaseSetting.field.label',
				'field'       => 'p.label',
				'selectLabel' => 'p.label',
			], 'select');

		$list

			->addAction([
				'href'      => function ($row) use ($project) {
					if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.phase.setting.edit', ['id' => $project->getId(), 'idPhaseSetting' => $row['phase']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($translator, $project) {
					if ($this->security->isGranted('PROJECT_WRITE', $project)) {
                        return '<i class="fa fa-edit" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.PhaseSetting.messageUpdated') . '"></i>';

                    } else {
						return '';
					}
				},
			])

			->addAction([
				'href'      => function ($row) use ($project) {
					if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
						if (null === $row['phase']->getDeletedAt()) {
							if ($this->security->isGranted('PHASESETTING_ARCHIVE', $row['phase'])) {
								return $this->router->generate('project.phase.setting.archive', ['id' => $project->getId(), 'idPhaseSetting' => $row['phase']->getId()]);
							} else {
								return '';
							}
						} else {
							return $this->router->generate('project.phase.setting.restore', ['id' => $project->getId(), 'idPhaseSetting' => $row['phase']->getId()]);
						}
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project, $translator) {
					if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
						if (null == $row['phase']->getDeletedAt()) {
							if ($this->security->isGranted('PHASESETTING_ARCHIVE', $row['phase'])) {
								return '<i class="fa fa-archive"></i>';
							} else {
								return '<i class="fa fa-archive disabled" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.PhaseSetting.messageArchived') . '"></i>';
							}
						} else {
							return '<i class="fa fa-box-open"></i>';
						}
					} else {
						return '';
					}
				},
			]);

		return $list;
	}
}
