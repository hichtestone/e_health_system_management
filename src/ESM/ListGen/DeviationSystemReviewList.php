<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationSystemReviewList
 * @package App\ListGen
 */
class DeviationSystemReviewList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(DeviationSystem $deviationSystem, TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(DeviationSystemReview::class);
		$url 		= 'deviation.system.review.list.ajax';
		$urlArgs 	= ['deviationSystemID' => $deviationSystem->getId()];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('deviation-system-review-list-table')
			->setClass('table')
			->setRowData(['id' => 'id'])
			->setRepository($repository)
			->setRepositoryMethod('indexListGenCrex', [$deviationSystem])
			->setExportFileName('deviation-system-' . $deviationSystem->getId() . '-reviews')
			->addConstantSort('deviation_system_review.id', 'ASC')
			->addHiddenData([
				'field' => 'deviation_system_review',
				'alias' => 'deviationSystemReview',
			]);

		$list
			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.number_crex',
				'field' => 'deviation_system_review.number',
				'formatter' => function ($row) {
					return $row['number'];
				},
				'formatter_csv' => function ($row) {
					return $row['number'];
				}
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.doneAt',
				'field' => 'deviation_system_review.doneAt',
				'formatter' => function ($row) {
					return null !== $row['doneAt'] ? $row['doneAt']->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['doneAt'] ? $row['doneAt']->format('d/m/Y') : '';
				}
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.typeReview',
				'field' => 'deviation_system_review.type',
				'formatter' => function ($row) use ($translator){
					return $row['type'] ? $translator->trans(DeviationReview::TYPE[$row['type']]) : '';
				},
				'formatter_csv' => function ($row) use ($translator){
					return $row['type'] ? $translator->trans(DeviationReview::TYPE[$row['type']]) : '';
				}
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.reader',
				'field' => 'reader.id',
				'formatter' => function ($row) {
					return $row['deviationSystemReview']->getReader()->getFirstName().' '.$row['deviationSystemReview']->getReader()->getLastName();
				},
				'formatter_csv' => function ($row) {
					return $row['deviationSystemReview']->getReader()->getFirstName().' '.$row['deviationSystemReview']->getReader()->getLastName();
				}
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.status',
				'field' => 'deviation_system_review.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['deviationSystemReview']->getStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['deviationSystemReview']->getStatus()]);
				}
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.validatedAt',
				'field' => 'deviation_system_review.validatedAt',
				'formatter' => function ($row) {
					return null !== $row['validatedAt'] ? $row['validatedAt']->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['validatedAt'] ? $row['validatedAt']->format('d/m/Y') : '';
				}
			])

			->addAction([
				'href' => function ($row) {
					if ($this->security->isGranted('DEVIATION_SYSTEM_REVIEW_SHOW', $row['deviationSystemReview'])) {
						return $this->router->generate('deviation.system.review.show', ['deviationSystemID' => $row['deviationSystemReview']->getDeviationSystem()->getId(), 'deviationSystemReviewID' => $row['deviationSystemReview']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function () {
					return '<i class="fa fa-eye c-grey"></i>';
				},
			])
		;

		return $list;
	}
}
