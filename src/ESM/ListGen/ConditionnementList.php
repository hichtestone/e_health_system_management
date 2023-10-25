<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\SchemaCondition;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConditionnementList.
 */
class ConditionnementList extends AbstractListGenType
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
		$repository = $this->em->getRepository(SchemaCondition::class);

		$url     = 'project.conditionnement.index.ajax'; // Url Ajax
		$urlArgs = ['id' => $project->getId()]; // Parametres de la method dans repository

		// Setup
		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'sc',
				'alias' => 'condition',
			])
			->addHiddenData([
				'field' => 'sc.id',
			])
			->setRepository($repository) // Repository Class
			->setRepositoryMethod('indexListGen', [$project]) // Method dans repository de l'entity
			->setExportFileName('condition') // Nom du fichier d'export CSV
		;

		// --- Colonnes ---

		// label
		$list
			->addColumn([
				'label'         => 'entity.SchemaCondition.field.label',
				'field'         => 'sc.label',
				'formatter'     => function ($row) {
					return '<a href="' . $this->router->generate('project.conditionnement.show', [
							'id'        => $row['condition']->getProject()->getId(),
							'condition' => $row['condition']->getId(),
						]) . '" >' . $row['condition'] . '</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['condition'];
				},
			]);

		// Filtre nom
		$list->addFilter([
			'label' => 'entity.SchemaCondition.field.label',
			'field' => 'sc.label',
		], 'string');

		// Visite
		/* $list
			 ->addColumn([
				 'label' => 'entity.SchemaCondition.field.visits',
				 'field' => 'v.short',
			 ]);*/

		$list->addColumn([
			'label'         => 'entity.SchemaCondition.field.visits',
			'field'         => 'v.short',
			'formatter'     => function ($row) {
				return $row['condition']->getVisit() ? $row['condition']->getVisit()->getShort() : '';
			},
			'formatter_csv' => function ($row) {
				return $row['condition']->getVisit() ? $row['condition']->getVisit()->getShort() : '';
			},
		]);

		// Phase
		$list->addColumn([
			'label'         => 'entity.SchemaCondition.field.phases',
			'field'         => 'p.label',
			'formatter'     => function ($row) {
				return $row['condition']->getPhase() ? $row['condition']->getPhase()->getLabel() : '';
			},
			'formatter_csv' => function ($row) {
				return $row['condition']->getPhase() ? $row['condition']->getPhase()->getLabel() : '';
			},
		]);


		// --- Filtres ---

		// Enable/disable
		/*$list->addFilter([
			'label' => 'entity.SchemaCondition.field.enable_disabled',
			'field' => 'sc.disabled',
		], 'select');*/

		// View
		$list->addAction([
			'href'      => function ($row) {
				return $this->router->generate('project.conditionnement.show', [
					'id'        => $row['condition']->getProject()->getId(),
					'condition' => $row['condition']->getId(),
				]);
			},
			'formatter' => function ($row) {
				return '<i class="fa fa-eye"></i>';
			},
		]);

		// --- Actions ---
		// Copier
		$list->addAction([
			'href'      => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.conditionnement.copy', [
						'id'        => $row['condition']->getProject()->getId(),
						'condition' => $row['condition']->getId(),
					]);
				} else {
					return '';
				}
			},
			'formatter' => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return '<i class="fa fa-copy"></i>';
				} else {
					return '';
				}

			},
		]);

		// Editer
		$list->addAction([
			'href'      => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.conditionnement.edit', [
						'id'        => $row['condition']->getProject()->getId(),
						'condition' => $row['condition']->getId(),
					]);
				} else {
					return '';
				}

			},
			'formatter' => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return '<i class="fa fa-edit"></i>';

				} else {
					return '';
				}

			},
		]);

		// enable/disable
		$list->addAction([
			'ajax'           => true,
			'afterUpdEffect' => [7],
			'title'          => function ($row) {
				if ($row['condition']->getDisabled()) {
					return $this->translator->trans('form.enable');
				}

				return $this->translator->trans('form.disable');
			},
			'href'           => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.conditionnement.enable_disable', [
						'id'        => $row['condition']->getProject()->getId(),
						'condition' => $row['condition']->getId(),
					]);
				} else {
					return '';
				}

			},
			'formatter'      => function ($row) use ($project) {
				if ($this->security->isGranted('IDENTIFICATIONVARIABLE_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					if ($row['condition']->getDisabled()) {
						return '<i class="fa fa-times c-red"></i>';
					}
					return '<i class="fa fa-check c-green"></i>';
				} else {
					return '';
				}

			},
		]);

		return $list;
	}
}
