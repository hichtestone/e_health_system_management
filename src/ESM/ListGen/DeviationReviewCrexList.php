<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\Project;
use App\ESM\Repository\DeviationReviewCrexRepository;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationReviewCrexList.
 */
class DeviationReviewCrexList extends AbstractListGenType
{
	/**
	 * @param $user
	 * @param string $locale
	 * @param DeviationReviewCrexRepository $crexRepository
	 * @param TranslatorInterface $translator
	 * @return mixed
	 */
    public function getListCrexPD($user, string $locale, DeviationReviewCrexRepository $crexRepository, TranslatorInterface $translator)
    {
        $url = 'no_conformity.protocol_deviation_crex.ajax';

        $list = $this->lg->setAjaxUrl($this->router->generate($url))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'dr',
                'alias' => 'review',
            ])
            ->addHiddenData([
                'field' => 'dr.id',
            ])
            ->setRepository($crexRepository)
            ->setRepositoryMethod('indexListGen', [$user, $locale])
            ->setExportFileName('reviews')
            ->addConstantSort('dr.id', 'ASC');

        $list
            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.number_crex',
                'field' => 'dr.numberCrex',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.project',
                'formatter' => function ($row) {
                    return $row['review']->getDeviation()->getProject()->getName();
                },
				'formatter_csv' => function ($row) {
            		return $row['review']->getDeviation()->getProject()->getName() ?? '';
				},
                'sortField' => 'project.name',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.reference',
                'formatter' => function ($row) {
                    $deviationID = $row['review']->getDeviation()->getId();
                    $projrctID = $row['review']->getDeviation()->getProject()->getId();
                    $url = $this->router->generate('deviation.declaration', ['projectID' => $projrctID, 'deviationID' => $deviationID]);

                    return '<a style="font-size: 0.8em;" href="'.$url.'" title="Voir fiche">'.$row['review']->getDeviation()->getCode().'</a>';
                },
				'formatter_csv' => function ($row) {
					return $row['review']->getDeviation()->getCode();
				},
                'sortField' => 'dr.deviation.code',
            ])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.observedAt',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) {
					return null !== $row['review']->getDeviation()->getObservedAt() ? $row['review']->getDeviation()->getObservedAt()->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['review']->getDeviation()->getObservedAt() ? $row['review']->getDeviation()->getObservedAt()->format('d/m/Y') : '';
				},
				'sortField' => 'dr.observedAt',
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.resume',
				'formatter' => function ($row) {
					return $row['review']->getDeviation()->getDescription() ?? '';
				},
				'formatter_csv' => function ($row) {
					return $row['review']->getDeviation()->getDescription() ?? '';
				},
				'sortField' => 'dr.deviation.description',
			])

			->addColumn([
				'label' => 'entity.Deviation.DeviationReview.field.status_conformity',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
				},
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
				},
				'sortField' => 'dr.status',
			])

			->addAction([
				'href' => function ($row) {
					return $this->router->generate('no_conformity.protocol_crex.show', ['reviewCrexID' => $row['review']->getId()]);
				},
				'formatter' => function () {
					return '<i class="fa fa-eye c-grey"></i>';
				},
			])

			->addFilter([
				'label' => 'entity.Deviation.DeviationReview.field.project',
				'field' => 'p.name',
				'selectLabel' => 'p.name',
			], 'select')

			->addFilter([
				'label' => 'entity.Deviation.DeviationReview.field.status',
				'field' => 'dr.status',
				'selectLabel' => function ($item) use ($translator) {
					return (DeviationReview::STATUS_EDITION == $item) ? $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_EDITION]) : $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_FINISH]);
				},
			], 'select')

			->addFilter([
			'label' => 'entity.Deviation.DeviationReview.field.observed_at',
			'field' => 'd.observedAt',
			], 'dates')

			->addFilter([
			'label' => 'entity.Deviation.DeviationReview.field.number_crex',
			'field' => 'dr.number',
			'selectLabel' => 'dr.number',
			], 'select')

			->addFilter([
			'label' => 'entity.Deviation.DeviationReview.field.code',
			'field' => 'd.code',
			'selectLabel' => 'd.code',
			], 'select')

			->addFilter([
			'label' => 'entity.Deviation.DeviationReview.field.resume',
			'field' => 'd.description',
			'selectLabel' => 'd.description',
			], 'select')
		;

        return $list;
    }

    /**
     * @return mixed
     */
    public function getList(Project $project, Deviation $deviation, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(DeviationReview::class);
        $url = 'deviation.review.crex.ajax';
        $urlArgs = ['projectID' => $project->getId(), 'deviationID' => $deviation->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'dr',
                'alias' => 'review',
            ])
            ->addHiddenData([
                'field' => 'dr.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGenCrex', [$project, $deviation])
            //->setExportFileName('reviews')
            ->addConstantSort('dr.id', 'ASC');

        $list
            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.number_crex',
                'field' => 'dr.number',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.doneAt',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return $row['review']->getDoneAt() ? $row['review']->getDoneAt()->format('d/m/Y') : '';
                },
				'formatter_csv' => function ($row) {
					return $row['review']->getDoneAt() ? $row['review']->getDoneAt()->format('d/m/Y') : '';
				},
                'sortField' => 'dr.doneAt',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.type',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($translator) {
                    return $translator->trans(DeviationReview::TYPE[$row['review']->getType()]);
                },
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::TYPE[$row['review']->getType()]);
				},
                'sortField' => 'dr.type',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.reader',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return $row['review']->getReader() ? $row['review']->getReader()->getFirstName().' '.$row['review']->getReader()->getLastName() : '';
                },
				'formatter_csv' => function ($row) {
					return $row['review']->getReader() ? $row['review']->getReader()->getFirstName().' '.$row['review']->getReader()->getLastName() : '';
				},
                'sortField' => 'r.firstName',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.status',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($translator) {
                    return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
                },
				'formatter_csv' => function ($row) use ($translator) {
					return $translator->trans(DeviationReview::STATUS[$row['review']->getStatus()]);
				},
                'sortField' => 'dr.status',
            ])

            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.validatedAt',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return $row['review']->getValidatedAt() ? $row['review']->getValidatedAt()->format('d/m/Y') : '';
                },
				'formatter_csv' => function ($row) {
					return $row['review']->getValidatedAt() ? $row['review']->getValidatedAt()->format('d/m/Y') : '';
				},
                'sortField' => 'dr.validatedAt',
            ])
		;

        if ($this->security->isGranted('ROLE_DEVIATION_READ')) {
            $list->addAction([
                    'href' => function ($row) {
                        return $this->router->generate('deviation.review.crex.show', ['projectID' => $row['review']->getDeviation()->getProject()->getId(), 'deviationID' => $row['review']->getDeviation()->getId(), 'reviewID' => $row['review']->getId()]);
                    },
                    'formatter' => function () {
                        return '<i class="fa fa-eye c-grey"></i>';
                    },
                ]);
        }

        return $list;
    }
}
