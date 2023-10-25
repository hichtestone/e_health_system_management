<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\Training;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class TrainingList.
 */
class TrainingList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(Training::class);
        $url = 'project.training.index.ajax';
		$urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 't',
                'alias' => 'training',
            ])
            ->addHiddenData([
                'field' => 't.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGenProjectTraining', [$project->getId()])
            ->setExportFileName('trainings')
            ->addConstantSort('t.id', 'ASC')
        ;
        $list->addColumn([
            'label' => 'entity.Training.field.title',
            'formatter' => function ($row) use ($project) {
                return '<a href="'.$this->router->generate('project.training.show', ['id' => $project->getId(), 'idTraining' => $row['training']->getId()]).'">'.
                    $row['training']->getTitle().'</a>';
            },
            'formatter_csv' => function ($row) use ($project) {
                return $row['training']->getTitle();
            },
            'sortField' => 't.title',
        ]);

        $list->addColumn([
            'label' => 'entity.Training.field.material',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['training']->getMaterial()) ? $row['training']->getMaterial() : '';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['training']->getMaterial()) ? $row['training']->getMaterial() : '';
            },
            'sortField' => 't.title',
        ]);

        $list->addColumn([
            'label' => 'entity.Training.field.startedAt',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['training']->getStartedAt()) ? date_format($row['training']->getStartedAt(), 'd/m/Y') : ' ';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['training']->getStartedAt()) ? date_format($row['training']->getStartedAt(), 'd/m/Y') : ' ';
            },
            'sortField' => 't.staredAt',
        ]);

        $list->addColumn([
            'label' => 'entity.Training.field.endedAt',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['training']->getEndedAt()) ? date_format($row['training']->getEndedAt(), 'd/m/Y') : ' ';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['training']->getEndedAt()) ? date_format($row['training']->getEndedAt(), 'd/m/Y') : ' ';
            },
            'sortField' => 't.endedAt',
        ]);

        $list->addAction([ // enable/disable
            'displayer' => function ($row, $security) {
                return $security->isGranted('ROLE_COMMUNICATION_READ');
            },
            'href' => function ($row) use ($project) {
                return $this->router->generate('project.training.show', ['id' => $project->getId(), 'idTraining' => $row['training']->getId()]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-eye c-grey"></i>';
            },
        ]);

        $list->addAction([ // enable/disable
			'href' => function ($row) use ($project) {
				if ($this->security->isGranted('TRAINING_DELETE', $row['training']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.training.delete', ['id' => $project->getId(), 'idTraining' => $row['training']->getId()]);
				} else {
					return '';
				}
			},
			'formatter' => function ($row) use ($project) {
				if ($this->security->isGranted('TRAINING_DELETE', $row['training']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return '<i class="fa fa-times c-red"></i>';
				} else {
					return '';
				}
			},
        ]);

        $list->addFilter([
            'label' => 'entity.Training.field.title',
            'field' => 't.id',
            'selectLabel' => 't.title',
        ], 'select')
        ;

        return $list;
    }
}
