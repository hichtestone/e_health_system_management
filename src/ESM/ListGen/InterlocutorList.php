<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Interlocutor;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class InterlocutorList.
 */
class InterlocutorList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, $projectId)
    {
        $repository = $this->em->getRepository(Interlocutor::class);
        $url = 'project.interlocutor.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'i',
                'alias' => 'interlocutor',
            ])
            ->addHiddenData([
                'field' => 'i.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGenProjectInterlocutor', [$projectId])
            ->setExportFileName('interlocutors')
            ->addConstantSort('i.id', 'ASC')
           /* ->addMultiAction([
                'label' => 'Contact',
                'href' => $this->router->generate('project.participant.contact.attach_mass_popup', ['id' => $projectId]),
                'displayer' => function ($security) {
                    return $security->isGranted('ROLE_PROJECT_WRITE');
                },
            ])*/
        ;
        // colonnes
        $list->addColumn([ //civilité
            'label' => 'entity.Interlocutor.field.civility',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['interlocutor']->getCivility();
            },
            'sortField' => 'i.civility',
        ]);

        return $list;
    }
}
