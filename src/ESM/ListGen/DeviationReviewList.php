<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationReviewList.
 */
class DeviationReviewList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(Project $project, Deviation $deviation, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(DeviationReview::class);
        $url = 'deviation.review.ajax';
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
            ->setRepositoryMethod('indexListGen', [$project, $deviation])
            ->setExportFileName('deviations')
            ->addConstantSort('dr.id', 'ASC');

        $list
            ->addColumn([
                'label' => 'entity.Deviation.DeviationReview.field.number',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($deviation, $project) {
                    if (($deviation::GRADE_MAJEUR === $row['review']->getDeviation()->getGrade() || $deviation::GRADE_CRITIQUE === $row['review']->getDeviation()->getGrade())) {
                        return '<a href="'.$this->router->generate('deviation.review.show',
                                ['projectID' => $project->getId(), 'deviationID' => $deviation->getId(), 'reviewID' => $row['review']->getId()]).'">'.$row['review']->getNumber().'</a>';
                    } else {
                        return $row['review']->getNumber();
                    }
                },
				'formatter_csv' => function ($row) use ($deviation, $project) {
					return $row['review']->getNumber();
				},
                'sortField' => 'dr.number',
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
                    return $row['review']->getReader()->getFirstName().' '.$row['review']->getReader()->getLastName();
                },
				'formatter_csv' => function ($row) {
					return $row['review']->getReader()->getFirstName().' '.$row['review']->getReader()->getLastName();
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
            ->addAction([
                'href' => function ($row) use ($deviation, $project) {
                    return $this->router->generate('deviation.review.show', ['projectID' => $project->getId(), 'deviationID' => $deviation->getId(), 'reviewID' => $row['review']->getId()]);
                },
                'formatter' => function () {
                    return '<i class="fa fa-eye c-grey"></i>';
                },
            ])
            ->addAction([
                'href' => function ($row) use ($deviation, $project) {
                    // role = "Revue déviation protocolaire" et grade = "Majeur" ou "Critique"
                    if ($this->security->isGranted('DEVIATION_REVIEW_EDIT', $row['review']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
                        return $this->router->generate('deviation.review.edit', ['projectID' => $project->getId(), 'deviationID' => $deviation->getId(), 'reviewID' => $row['review']->getId()]);
                    } else {
                        return '';
                    }
                },
                'formatter' => function ($row) {
                    if ($this->security->isGranted('DEVIATION_REVIEW_EDIT', $row['review'])) {
                        return '<i class="fa fa-edit"></i>';
                    } else {
                        return '';
                    }
                },
            ])
            ->addFilter([
                'label' => 'entity.Deviation.DeviationReview.field.number',
                'field' => 'dr.number',
                'selectLabel' => 'dr.number',
            ], 'select')
            ->addFilter([
                'label' => 'entity.Deviation.DeviationReview.field.type',
                'field' => 'dr.type',
                'selectLabel' => function ($item) use ($translator) {
                    switch ($item) {
                        case DeviationReview::TYPE_OPERATIONAL:
                            return $translator->trans(DeviationReview::TYPE[DeviationReview::TYPE_OPERATIONAL]);
                        case DeviationReview::TYPE_QUALITY_CONTROL:
                            return $translator->trans(DeviationReview::TYPE[DeviationReview::TYPE_QUALITY_CONTROL]);
                        case DeviationReview::TYPE_CREX:
                            return $translator->trans(DeviationReview::TYPE[DeviationReview::TYPE_CREX]);
                    }
                },
            ], 'select')
            ->addFilter([
                'label' => 'entity.Deviation.DeviationReview.field.reader',
                'field' => 'r.lastName',
                'selectLabel' => 'r.lastName',
            ], 'select')
            ->addFilter([
                'label' => 'entity.Deviation.DeviationReview.field.status',
                'field' => 'dr.status',
                'selectLabel' => function ($item) use ($translator) {
                    return (DeviationReview::STATUS_EDITION == $item) ? $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_EDITION]) : $translator->trans(DeviationReview::STATUS[DeviationReview::STATUS_FINISH]);
                },
            ], 'select');

        return $list;
    }
}
