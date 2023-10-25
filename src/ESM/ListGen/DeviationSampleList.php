<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationSampleList.
 */
class DeviationSampleList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(DeviationSample::class);
		$url        = 'no_conformity.deviation.sample.index.ajax';
		$urlArgs    = [];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('deviation-sample-list-table')
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'sample',
				'alias' => 'deviationSample',
			])
			->addHiddenData([
				'field' => 'sample.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen')
			->setExportFileName('deviationSamples')
			->addConstantSort('sample.id', 'ASC')
			->addColumn([
				'label' => '',
				'field' => 'sample.id',
				'formatter' => function () {
					return '<input type="checkbox"/>';
				}
			])
			->addColumn([
				'label' => 'entity.deviationSample.label',
				'field' => 'sample.code',
				'formatter' => function ($row)  {
					$deviationSampleID = $row['id'];
					if (Deviation::STATUS_DRAFT === $row['deviationSample']->getStatus()) {
						$url = $this->router->generate('no_conformity.deviation.sample.declaration', ['deviationSampleID' => $deviationSampleID, 'edit' => 'edit']);
					} else {
						$url = $this->router->generate('no_conformity.deviation.sample.declaration', ['deviationSampleID' => $deviationSampleID]);
					}

					return '<a style="font-size: 0.8em;" href="' . $url . '" title="Voir fiche">' . $row['code'] . '</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['code'];
				},
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.project',
				'field' => 'project.name',
				'formatter' => function ($row) {
					$arr = array_map(function ($project) {
							return $project->getName();
					}, $row['deviationSample']->getProjects()->toArray());

					return implode(', ', array_filter($arr));
				},
				'formatter_csv' => function ($row) {
					$arr = array_map(function ($project) {
						return $project->getName();
					}, $row['deviationSample']->getProjects()->toArray());

					return implode('; ', $arr);
				},
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.observedAt',
				'field' => 'sample.observedAt',
				'formatter' => function ($row) {
					return $row['deviationSample']->getObservedAt() ? $row['deviationSample']->getObservedAt()->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return $row['deviationSample']->getObservedAt() ? $row['deviationSample']->getObservedAt()->format('d/m/Y') : '';
				},
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.processInvolves',
				'field' => 'processInvolves.label',
				'formatter' => function ($row) {
					$arr = array_map(function ($processInvolve) {
						return $processInvolve->getLabel();
					}, $row['deviationSample']->getProcessInvolves()->toArray());

					return implode(', ', array_filter($arr));
				},
				'formatter_csv' => function ($row) {
					$arr = array_map(function ($processInvolve) {
						return $processInvolve->getLabel();
					}, $row['deviationSample']->getProcessInvolves()->toArray());

					return implode('; ', $arr);
				},
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.resume',
				'field' => 'sample.resume',
				'formatter' => function ($row) {
					return $row['deviationSample']->getResume() ?? '';
				},
				'formatter_csv' => function ($row) {
					return $row['deviationSample']->getResume() ?? '';
				},
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(Deviation::STATUS[$row['deviationSample']->getStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(Deviation::STATUS[$row['deviationSample']->getStatus()]);
				},
				'sortField' => 'sample.status',
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.grade',
				'formatter' => function ($row) use ($translator) {
					return null != $row['deviationSample']->getGrade() ? $translator->trans(Deviation::GRADES[$row['deviationSample']->getGrade()]) : '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return null != $row['deviationSample']->getGrade() ? $translator->trans(Deviation::GRADES[$row['deviationSample']->getGrade()]) : '';
				},
				'sortField' => 'sample.grade',
			])
			->addColumn([
				'label' => 'entity.deviationSample.field.closedAt',
				'field' => 'sample.closedAt',
				'formatter' => function ($row) {
					return $row['deviationSample']->getClosedAt() ? $row['deviationSample']->getClosedAt()->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return $row['deviationSample']->getClosedAt() ? $row['deviationSample']->getClosedAt()->format('d/m/Y') : '';
				},
			])
			->addFilter([
				'label' => 'entity.deviationSample.label',
				'field' => 'sample.code',
				'selectLabel' => 'sample.code',
			], 'select')
			->addFilter([
				'label' => 'entity.deviationSample.field.project',
				'field' => 'project.name',
				'selectLabel' => function ($item) use ($translator) {
					return $item ?? '';
				},
			], 'select')
			->addFilter([
				'label' => 'entity.deviationSample.field.processInvolves',
				'field' => 'processInvolves.label',
				'selectLabel' => function ($item) use ($translator) {
					return $item ?? '';
				},
			], 'select')
			->addFilter([
				'label' => 'Grade',
				'field' => 'sample.grade',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case Deviation::GRADE_MINEUR:
							return $translator->trans(Deviation::GRADES[Deviation::GRADE_MINEUR]);
						case Deviation::GRADE_MAJEUR:
							return $translator->trans(Deviation::GRADES[Deviation::GRADE_MAJEUR]);
						case Deviation::GRADE_CRITIQUE:
							return $translator->trans(Deviation::GRADES[Deviation::GRADE_CRITIQUE]);
						default:
							return '';
					}
				},
			], 'select')
			->addFilter([
				'label' => 'entity.deviationSample.field.status',
				'field' => 'sample.status',
				'selectLabel' => function ($item) use ($translator) {
					switch ($item) {
						case Deviation::STATUS_DRAFT:
							return $translator->trans(Deviation::STATUS[Deviation::STATUS_DRAFT]);
						case Deviation::STATUS_IN_PROGRESS:
							return $translator->trans(Deviation::STATUS[Deviation::STATUS_IN_PROGRESS]);
						case Deviation::STATUS_DONE:
							return $translator->trans(Deviation::STATUS[Deviation::STATUS_DONE]);
					}
				},
			], 'select')
		;

		return $list;
	}
}
