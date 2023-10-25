<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Contact;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class ContactList.
 */
class ContactList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(Contact::class);
        $url = 'project.contact.index.ajax';
        $urlArgs = ['id' => $project->getId()];

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
            ->setRepositoryMethod('indexListGenProjectContact', [$project->getId()])
            ->setExportFileName('contacts')
            ->addConstantSort('c.id', 'ASC')
        ;
        $list
            ->addAction([ // show
                 'displayer' => function ($row, $security) {
                     return $security->isGranted('ROLE_COMMUNICATION_READ');
                 },
                 'href' => function ($row) use ($project) {
                     return $this->router->generate('project.contact.show', ['id' => $project->getId(), 'idContact' => $row['contact']->getId()]);
                 },
                 'formatter' => function ($row) {
                     return '<i class="fa fa-eye c-grey"></i>';
                 },
            ])
            ->addAction([ // delete
				'href' => function ($row) use ($project) {
					if ($this->security->isGranted('CONTACT_DELETE', $row['contact']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return $this->router->generate('project.contact.delete', ['id' => $project->getId(), 'idContact' => $row['contact']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) use ($project) {
					if ($this->security->isGranted('CONTACT_DELETE', $row['contact']) && $this->security->isGranted('PROJECT_WRITE', $project)) {
						return '<i class="fa fa-times c-red"></i>';
					} else {
						return '';
					}
				},

            ])

            ->addColumn([
                'label' => 'entity.Contact.field.object',
                'formatter' => function ($row) use ($project) {
                    return '<a href="'.$this->router->generate('project.contact.show', [
                        'id' => $project->getId(), 'idContact' => $row['contact']->getId(), ]).'">'
                        .$row['contact']->getObject().'</a>';
                },
                'formatter_csv' => function ($row) {
                    return $row['contact']->getObject();
                },
                'sortField' => 'c.object',
            ])
            ->addColumn([
                'label' => 'entity.Contact.field.type_recipient',
                'formatter' => function ($row){
                    return $row['contact']->getTypeRecipient();
                },
                'formatter_csv' => function ($row) {
                    return $row['contact']->getTypeRecipient();
                },
                'sortField' => 'c.typeRecipient',
            ])
            ->addColumn([
                'label' => 'entity.Contact.field.type',
                'formatter' => function ($row) {
                    return $row['contact']->getType();
                },
                'formatter_csv' => function ($row) {
                    return $row['contact']->getType();
                },
                'sortField' => 'c.type',
            ])
            ->addColumn([
                'label' => 'entity.Contact.field.completed',
                'formatter' => function ($row) {
                    return (true == $row['contact']->isCompleted()) ? 'Oui' : 'Non';
                },
                'formatter_csv' => function ($row) {
                    return (true == $row['contact']->isCompleted()) ? 'Oui' : 'Non';
                },
                'sortField' => 'c.completed',
            ])
            ->addColumn([
                'label' => 'entity.Contact.field.date',
                'formatter' => function ($row) {
                    return !is_null($row['contact']->getDate()) ? date_format($row['contact']->getDate(), 'd/m/Y') : ' ';
                },
                'formatter_csv' => function ($row) {
                    return !is_null($row['contact']->getDate()) ? date_format($row['contact']->getDate(), 'd/m/Y') : ' ';
                },
                'sortField' => 'c.date',
            ])
            ->addColumn([
                'label' => 'entity.Contact.field.hour',
                'field' => 'c.hour',
            ])
            ->addFilter([
                'label' => 'entity.Contact.field.type_recipient',
                'field' => 'r.id',
                'selectLabel' => 'r.label',
            ], 'select')
            ->addFilter([
                'label' => 'entity.Contact.field.type',
                'field' => 't.id',
                'selectLabel' => 't.label',
            ], 'select')
            ;

        return $list;
    }
}
