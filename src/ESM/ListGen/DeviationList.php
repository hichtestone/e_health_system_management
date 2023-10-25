<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DeviationList.
 */
class DeviationList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(Project $project, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(Deviation::class);
        $url 		= 'deviation.list.ajax';
        $urlArgs 	= ['projectID' => $project->getId()];
        $list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                                ->setId('deviation-list-table')
                                ->setClass('table')
                                ->setRowData(['id' => 'id'])->addHiddenData([
									'field' => 'deviation',
									'alias' => 'dev',
								])
                                ->addHiddenData(['field' => 'deviation.id'])
                                ->setRepository($repository)
                                ->setRepositoryMethod('indexListGen', [$project])
                                ->setExportFileName('deviations')
                                ->addConstantSort('deviation.id', 'ASC')

            ->addColumn([
                'label' => 'entity.Deviation.field.code_deviation',
                'field' => 'deviation.code',
                'formatter' => function ($row) use ($project) {
                     $deviationID = $row['id'];
                        if (Deviation::STATUS_DRAFT === $row['status']) {
                            $url = $this->router->generate('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $deviationID, 'edit' => 'edit']);
                        } else {
                            $url = $this->router->generate('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $deviationID]);
                        }

                 return '<a style="font-size: 0.8em;" href="'.$url.'" title="Voir fiche">'.$row['code'].'</a>';
                },
				'formatter_csv' => function ($row) use ($project) {
					return $row['code'];
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.center',
                'field' => 'center.name',
				'formatter_csv' => function ($row) {
					return $row['name'] ?? '';
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.observed_at',
                'field' => 'deviation.observedAt',
                'formatter' => function ($row) {
                    return null !== $row['observedAt'] ? $row['observedAt']->format('d/m/Y') : '';
                },
				'formatter_csv' => function ($row) {
					return null !== $row['observedAt'] ? $row['observedAt']->format('d/m/Y') : '';
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.resume',
                'field' => 'deviation.resume',
				'formatter_csv' => function ($row) {
					return $row['resume'] ?? '';
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.grade',
                'field' => 'deviation.grade',
				'formatter' => function ($row) use ($translator) {
					return $row['grade'] ?  $translator->trans(Deviation::GRADES[$row['grade']]) : '';
				},
				'formatter_csv' => function ($row) {
					return $row['grade'] ?  Deviation::GRADES[$row['grade']] : '';
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.status',
                'field' => 'deviation.status',
                'formatter' => function ($row) use ($translator) {
                    return $translator->trans(Deviation::STATUS[$row['status']]);
                },
				'formatter_csv' => function ($row) {
					return Deviation::STATUS[$row['status']];
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.crex',
                'field' => 'reviews.number',
                'formatter' => function ($row) {
                   return count($row['dev']->getReviews()->toArray()) > 0  ? 'Oui' : 'Non';
                },
				'formatter_csv' => function ($row) {
					return count($row['dev']->getReviews()->toArray()) > 0  ? 'Oui' : 'Non';
				},
            ])
            ->addColumn([
                'label' => 'entity.Deviation.field.closed_at',
                'field' => 'deviation.closedAt',
                'formatter' => function ($row) {
                    return null !== $row['closedAt'] ? $row['closedAt']->format('d/m/Y') : '';
                },
				'formatter_csv' => function ($row) {
					return null !== $row['closedAt'] ? $row['closedAt']->format('d/m/Y') : '';
				},
            ]);
        if ($this->security->isGranted('ROLE_DEVIATION_READ')) {
            $list
                ->addAction([
                    'href' => function ($row) use ($project) {
						if ($row['status'] === Deviation::STATUS_DRAFT) {
							return $this->router->generate('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $row['id'], 'edit' => 'edit']);
						} else {
							return $this->router->generate('deviation.declaration', ['projectID' => $project->getId(), 'deviationID' => $row['id']]);
						}
                    },
                    'formatter' => function () {
                        return '<i class="fa fa-eye c-grey"></i>';
                    },
                ])
                ->addAction([
                    'href' => function ($row) use ($project) {
                        return $this->router->generate('deviation.review', ['projectID' => $project->getId(), 'deviationID' => $row['id']]);
                    },
                    'formatter' => function () {
                        return '<i class="fa fa-list-alt c-grey"></i>';
                    },
                ])
                ->addAction([
                    'href' => function ($row) use ($project) {
                        return $this->router->generate('deviation.sample', ['projectID' => $project->getId(), 'deviationID' => $row['id']]);
                    },
                    'formatter' => function () {
                        return '<i class="fa fa-stop-circle c-grey"></i>';
                    },
                ]);
        }

        $list->addFilter([
            'label' => 'Code déviation',
            'field' => 'deviation.code',
            'selectLabel' => 'deviation.code',
        ], 'select')
            ->addFilter([
                'label' => 'Centre',
                'field' => 'center.name',
                'selectLabel' => 'center.name',
            ], 'select')
            ->addFilter([
                'label' => 'Date de constat',
                'field' => 'deviation.observedAt',
                'selectLabel' => 'deviation.observedAt',
            ], 'date')
            ->addFilter([
                'label' => 'Grade',
                'field' => 'deviation.grade',
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
                'label' => 'Statut',
                'field' => 'deviation.status',
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
				'label' => 'Soumission CREX',
				'field' => 'deviation.isCrexSubmission',
				'defaultValues' => [2],
			], 'bool')
            ->addFilter([
                'label' => 'Date de clôture',
                'field' => 'deviation.closedAt',
                'selectLabel' => 'deviation.closedAt',
            ], 'date');

        if ($this->security->isGranted('ROLE_DEVIATION_REVIEW')) {
            $list->addMultiAction([
                'label' => 'Faire une revue multiple',
                'href' => $this->router->generate('deviation.review.multiple.new', ['projectID' => $project->getId()]),
                'displayer' => function ($security) {
                    return true;
                },
                'data' => ['redirect' => 'deviations_ids'],
            ]);
        }

        return $list;
    }
}
