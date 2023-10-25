<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Entity\Project;
use App\ESM\Repository\DeviationReviewCrexRepository;
use App\ESM\Repository\DeviationSystemReviewRepository;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationSystemReviewCrexList.
 */
class DeviationSystemReviewCrexList extends AbstractListGenType
{
	/**
	 * @param DeviationSystemReviewRepository $reviewRepository
	 * @param TranslatorInterface $translator
	 * @return mixed
	 */
    public function getList(DeviationSystemReviewRepository $reviewRepository, TranslatorInterface $translator)
    {
		$url        = 'no_conformity.system_crex.ajax';
		$urlArgs    = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setId('deviation-system-review-crex-table')
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'deviation_system_review',
                'alias' => 'review',
            ])
            ->addHiddenData([
                'field' => 'deviation_system_review.id',
            ])
            ->setRepository($reviewRepository)
            ->setRepositoryMethod('indexListGenCrexNCSystem')
            ->setExportFileName('systemReviewCrex')
            ->addConstantSort('deviation_system_review.id', 'ASC')
			->addColumn([
                'label' => 'entity.NoConformity.field.system.review.numberCrex',
                'field' => 'deviation_system_review.numberCrex',
            ])
			->addColumn([
				'label' => 'entity.NoConformity.field.system.review.code',
				'formatter' => function ($row) {
					$deviationSystemID = $row['review']->getDeviationSystem()->getId();
					$url = $this->router->generate('deviation.system.declaration', ['deviationSystemID' => $deviationSystemID]);

					return '<a style="font-size: 0.8em;" href="'.$url.'" title="Voir fiche">'.$row['review']->getDeviationSystem()->getCode().'</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['review']->getDeviationSystem()->getCode();
				},
				'sortField' => 'deviation_system.code',
			])
			->addColumn([
				'label' => 'entity.NoConformity.field.system.review.resume',
				'formatter' => function ($row) {
					return $row['review']->getDeviationSystem()->getResume() ?? '';
				},
				'formatter_csv' => function ($row) {
					return $row['review']->getDeviationSystem()->getResume() ?? '';
				},
				'sortField' => 'deviation_system.resume',
			])
			->addColumn([
				'label' => 'entity.NoConformity.field.system.review.grade',
				'field' => 'deviation_system.grade',
				'formatter' => function ($row) use ($translator) {
					return $row['review']->getDeviationSystem()->getGrade() ? $translator->trans(Deviation::GRADES[$row['review']->getDeviationSystem()->getGrade()]) : '';
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $row['review']->getDeviationSystem()->getGrade() ? $translator->trans(Deviation::GRADES[$row['review']->getDeviationSystem()->getGrade()]) : '';
				}
			])
			->addColumn([
				'label' => 'entity.NoConformity.field.system.review.statut',
				'field' => 'deviation_system_review.status',
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
				}
			])

			->addAction([
				'href' => function ($row) {
					return $this->router->generate('no_conformity.system_crex.show', ['reviewSystemCrexID' => $row['review']->getId()]);
				},
				'formatter' => function () {
					return '<i class="fa fa-eye c-grey"></i>';
				},
			])

			->addFilter([
				'label' => 'entity.NoConformity.field.system.review.numberCrex',
				'field' => 'deviation_system_review.numberCrex',
				'selectLabel' => 'deviation_system_review.numberCrex',
			], 'select')

			->addFilter([
				'label' => 'entity.NoConformity.field.system.review.code',
				'field' => 'deviation_system.code',
				'selectLabel' => 'deviation_system.code',
			], 'select')

			->addFilter([
				'label' => 'entity.NoConformity.field.system.review.resume',
				'field' => 'deviation_system.description',
				'selectLabel' => 'deviation_system.description',
			], 'select')

			->addFilter([
				'label' => 'entity.NoConformity.field.system.review.statut',
				'field' => 'deviation_system_review.status',
				'selectLabel' => function ($item) use ($translator) {
					return (DeviationReview::STATUS_EDITION == $item) ? $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_EDITION]) : $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_FINISH]);
				},
			], 'select')
		;

        return $list;
    }
}
