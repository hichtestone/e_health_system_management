<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class MeetingList.
 */
class ContactIntervenantList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(Contact::class);
        $url = 'project.contact.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'c',
                'alias' => 'contact',
            ])
            ->addHiddenData([
                'field' => 'c.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGenProjectIntervenantContact', [$project->getId()])
            ->setExportFileName('contacts')
            ->addConstantSort('c.id', 'ASC');
        $list->addColumn([
            'label' => 'entity.Contact.title',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) use ($project) {
                return '<a href="#">'.$row['contact']->getTitle().'</a>';
            },
            'sortField' => 'c.title',
        ]);

        $list->addColumn([
            'label' => 'entity.Contact.details',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) use ($project) {
                return '<a href="#">'.$row['contact']->getDetails().'</a>';
            },
            'sortField' => 'c.details',
        ]);

        $list->addColumn([
            'label' => 'entity.Contact.startedAt',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) use ($project) {
                return '<a href="#">'.date_format($row['contact']->getStartedAt(), 'd-m-Y H:i').'</a>';
            },
            'sortField' => 'c.startedAt',
        ]);

        $list->addColumn([
            'label' => 'entity.Contact.type',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) use ($project) {
                return '<a href="#">'.$row['contact']->getType().'</a>';
            },
            'sortField' => 'c.type',
        ]);

        return $list;
    }
}
