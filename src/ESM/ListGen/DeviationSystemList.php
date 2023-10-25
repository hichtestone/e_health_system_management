<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationSystemList.
 */
class DeviationSystemList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(DeviationSystem::class);
		$url 		= 'deviation.system.list.ajax';
		$urlArgs 	= [];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('deviation-system-list-table')
			->setClass('table')
			->setRowData(['id' => 'id'])
//			->addHiddenData(['field' => 'deviationSystem.id'])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [])
			->setExportFileName('deviations-system')
			->addConstantSort('system.id', 'ASC')
			->addHiddenData([
				'field' => 'system',
				'alias' => 'deviationSystem',
			])
//			->addHiddenData([
//				'field' => 'deviationSystem.id',
//			])

			->addColumn([
				'label' => '',
				'field' => 'system.id',
				'formatter' => function () {
					return '<input type="checkbox"/>';
				}
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.code',
				'field' => 'system.code',
				'formatter' => function ($row) {
					$deviationSystemID = $row['id'];
					if (Deviation::STATUS_DRAFT === $row['status']) {
						$url = $this->router->generate('deviation.system.declaration', ['deviationSystemID' => $deviationSystemID, 'edit' => 'edit']);
					} else {
						$url = $this->router->generate('deviation.system.declaration', ['deviationSystemID' => $deviationSystemID]);
					}

					return '<a style="font-size: 0.8em;" href="'.$url.'" title="Voir fiche">'.$row['deviationSystem']->getCode().'</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['code'];
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.observedAt',
				'field' => 'system.observedAt',
				'formatter' => function ($row) {
					return null !== $row['observedAt'] ? $row['observedAt']->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['observedAt'] ? $row['observedAt']->format('d/m/Y') : '';
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.process',
				'field' => 'process.code as process_code',
				'formatter' => function ($row) {
					$arr = array_map(function ($process) {return $process->getCode();}, $row['deviationSystem']->getProcess()->toArray());
					return implode(', ', array_filter($arr));
				},
				'formatter_csv' => function ($row) {
					$arr = array_map(function ($process) {return $process->getCode();}, $row['deviationSystem']->getProcess()->toArray());
					return implode('; ', $arr);
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.resume',
				'field' => 'system.resume',
				'formatter' => function ($row) {
					return $row['resume'] ?? '';
				},
				'formatter_csv' => function ($row) {
					return $row['resume'] ?? '';
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.grade',
				'field' => 'system.grade',
				'formatter' => function ($row) use ($translator) {
					return $row['grade'] ?  $translator->trans(Deviation::GRADES[$row['grade']]) : '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $row['grade'] ?  $translator->trans(Deviation::GRADES[$row['grade']]) : '';
				},
			])

			->addColumn([
				'label' => 'entity.Deviation.field.status',
				'field' => 'system.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(Deviation::STATUS[$row['status']]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(Deviation::STATUS[$row['status']]);
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.closedAt',
				'field' => 'system.closedAt',
				'formatter' => function ($row) {
					return null !== $row['closedAt'] ? $row['closedAt']->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['closedAt'] ? $row['closedAt']->format('d/m/Y') : '';
				},
			])

			->addColumn([
				'label' => 'entity.NoConformity.field.system.isCrexSubmission',
				'field' => 'system.isCrexSubmission',
				'formatter' => function ($row) use ($translator) {
					return count($row['deviationSystem']->getReviews()->toArray()) > 0  ? $translator->trans('general.yes') : $translator->trans('general.no');

				},
				'formatter_csv' => function ($row) use ($translator) {
					return count($row['deviationSystem']->getReviews()->toArray()) > 0  ? $translator->trans('general.yes') : $translator->trans('general.no');

				},
			])
		;

		$list
			->addFilter([
				'label' => 'entity.NoConformity.field.system.code',
				'field' => 'system.code',
				'selectLabel' => 'system.code',
			], 'select')
			->addFilter([
				'label' => 'entity.NoConformity.field.system.grade',
				'field' => 'system.grade',
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
				'label' => 'entity.Deviation.field.status',
				'field' => 'system.status',
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
			->addFilter([
				'label' => 'entity.NoConformity.field.system.isCrexSubmission',
				'field' => 'system.isCrexSubmission',
				'defaultValues' => [2],
			], 'bool')
			->addFilter([
				'label' => 'entity.NoConformity.field.system.closedAt',
				'field' => 'system.closedAt',
				'selectLabel' => 'system.closedAt',
			], 'date');
		;

		return $list;
	}
}
