<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Meeting;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class MeetingList.
 */
class MeetingList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(Meeting::class);
        $url = 'project.meeting.index.ajax';
        $urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'm',
                'alias' => 'meeting',
            ])
            ->addHiddenData([
                'field' => 'm.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGenProjectMeeting', [$project->getId()])
            ->setExportFileName('meetings')
            ->addConstantSort('m.id', 'ASC')
        ;
        $list->addColumn([
            'label' => 'entity.Meeting.field.title',
            'formatter' => function ($row) use ($project) {
                return '<a href="'.$this->router->generate('project.meeting.show', ['id' => $project->getId(), 'idMeeting' => $row['meeting']->getId()]).'">'.
                $row['meeting']->getType().'</a>';
            },
			'formatter_csv' => function ($row) use ($project) {
				return $row['meeting']->getType();
			},
            'sortField' => 'm.type',
        ]);

        $list->addColumn([
            'label' => 'entity.Meeting.field.report',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['meeting']->getReport()) ? $row['meeting']->getReport() : '';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['meeting']->getReport()) ? $row['meeting']->getReport() : '';
            },
            'sortField' => 'm.report',
        ]);

        $list->addColumn([
            'label' => 'entity.Meeting.field.startedAt',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['meeting']->getStartedAt()) ? date_format($row['meeting']->getStartedAt(), 'd/m/Y') : ' ';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['meeting']->getStartedAt()) ? date_format($row['meeting']->getStartedAt(), 'd/m/Y') : ' ';
            },
            'sortField' => 'm.startedAt',
        ]);

        $list->addColumn([
            'label' => 'entity.Meeting.field.duration',
            'formatter' => function ($row) use ($project) {
                return !is_null($row['meeting']->getDuration()) ? $row['meeting']->getDuration() : '';
            },
            'formatter_csv' => function ($row) use ($project) {
                return !is_null($row['meeting']->getDuration()) ? $row['meeting']->getDuration() : '';
            },
            'sortField' => 'm.duration',
        ]);

        $list->addAction([ // enable/disable
            'displayer' => function ($row, $security) {
                return $security->isGranted('ROLE_COMMUNICATION_READ');
            },
            'href' => function ($row) use ($project) {
                return $this->router->generate('project.meeting.show', ['id' => $project->getId(), 'idMeeting' => $row['meeting']->getId()]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-eye c-grey"></i>';
            },
        ]);

        $list->addAction([ // enable/disable
            'href' => function ($row) use ($project) {
				if ($this->security->isGranted('MEETING_DELETE', $row['meeting']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return $this->router->generate('project.meeting.delete', ['id' => $project->getId(), 'idMeeting' => $row['meeting']->getId()]);
				} else {
					return '';
				}
            },
            'formatter' => function ($row) use ($project) {
				if ($this->security->isGranted('MEETING_DELETE', $row['meeting']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
					return '<i class="fa fa-times c-red"></i>';
				} else {
					return '';
				}
            },
        ]);

        $list->addFilter([
            'label' => 'entity.Meeting.field.title',
            'field' => 'm.id',
            'selectLabel' => 'm.type',
        ], 'select')
        ;

        return $list;
    }
}
