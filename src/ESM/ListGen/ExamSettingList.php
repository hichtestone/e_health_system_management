<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Exam;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExamSettingList.
 */
class ExamSettingList extends AbstractListGenType
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
        $repository = $this->em->getRepository(Exam::class);
        $url = 'project.exam.setting.index.ajax';
        $urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                         ->setClass('table')
                         ->setRowData(['id' => 'id'])
                         ->addHiddenData([
                             'field' => 'e',
                             'alias' => 'exam',
                         ])
                         ->addHiddenData([
                             'field' => 'e.id',
                         ])
                         ->setRepository($repository)
                         ->setRepositoryMethod('indexListGen', [$project])
                         ->setExportFileName('exam-settings')
                         ->addConstantSort('e.position', 'ASC');

        $list
            ->addColumn([
                'label' => 'entity.ExamSetting.field.type',
                'formatter' => function ($row) {
                    return $row['exam']->getType()->getLabel();
                },
                'sortField' => 'e.type',
            ])
            ->addColumn([
                'label' => 'entity.ExamSetting.field.name',
                'field' => 'e.name',
            ])
            ->addColumn([
                'label' => 'entity.ExamSetting.field.price',
                'formatter' => function ($row) {
                    return $row['exam']->getPrice().'&euro;';
                },
                'sortField' => 'e.price',
            ])
            ->addFilter([
                'label' => 'entity.ExamSetting.field.name',
                'field' => 'e.id',
                'selectLabel' => 'e.name',
            ], 'select')
            ->addFilter([
                'label' => 'entity.ExamSetting.field.type',
                'field' => 't.id',
                'selectLabel' => 't.label',
            ], 'select');

        if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')) {
            $list->addAction([
            	'href' => function ($row) use ($project) {
					if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.exam.setting.edit', ['id' => $project->getId(), 'idExam' => $row['exam']->getId()]);
					} else {
						return '';
					}
                },
                'formatter' => function ($row) use ($translator, $project) {
					if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
                        return '<i class="fa fa-edit" href="#" data-placement="left" data-toggle="tooltip" title="' . $translator->trans('entity.ExamSetting.messageUpdated') . '"></i>';

                    } else {
						return '';
					}
                },
            ]);
        }

        $list->addAction([
            'href' => function ($row) use ($project) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					if (null === $row['exam']->getDeletedAt()) {
						if ($this->security->isGranted('EXAMSETTING_ARCHIVE', $row['exam'])) {
							return $this->router->generate('project.exam.setting.archive', ['id' => $project->getId(), 'idExam' => $row['exam']->getId()]);
						} else {
							return '';
						}
					} else {
						return $this->router->generate('project.exam.setting.restore', ['id' => $project->getId(), 'idExam' => $row['exam']->getId()]);
					}
				} else {
					return '';
				}
            },
            'formatter' => function ($row) use ($project, $translator) {
				if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE') && $this->security->isGranted('PROJECT_WRITE', $project)) {
					if (null == $row['exam']->getDeletedAt()) {
						if ($this->security->isGranted('EXAMSETTING_ARCHIVE', $row['exam'])) {
							return '<i class="fa fa-archive"></i>';
						} else {
							return '<i class="fa fa-archive disabled" href="#" data-placement="left" data-toggle="tooltip" title="'.$translator->trans('entity.ExamSetting.messageArchived').'"></i>';
						}
					} else {
						return '<i class="fa fa-box-open"></i>';
					}
				} else {
					return '';
				}
            },
        ])
            ->addColumn([
                'label' => 'entity.status.label',
                'formatter' => function ($row) {
                    return null === $row['exam']->getDeletedAt() ? 'Non archivé' : 'Archivé';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'e.deletedAt',
            ]);
			if ($this->security->isGranted('ROLE_DIAGRAMVISIT_WRITE')) {
				$list->addFilter([
					'label' => 'filter.archived.label',
					'translation_domain' => 'ListGen',
					'field' => 'e.deletedAt',
					'defaultValues' => [0],
				], 'archived');
			}

        return $list;
    }
}
