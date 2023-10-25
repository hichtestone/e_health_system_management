<?php

namespace App\ListGen;

use App\ESM\Entity\Document;
use App\Service\ListGen\AbstractListGenType;
use App\Service\ListGen\ListGen;

/**
 * Class DocumentList.
 */
class DocumentList extends AbstractListGenType
{
    public function getList(): ListGen
    {
        $rep = $this->em->getRepository(Document::class);
        $url = 'admin.document.index.ajax';
        $urlArgs = [];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'a',
                'alias' => 'document',
            ])
            ->addHiddenData([
                'field' => 'a.id',
            ])

            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen')
            ->addConstantSort('a.id', 'ASC')
        ;

        // colonnes
        $list->addColumn([
            'label' => 'entity.Document.list.thead.name',
            'formatter' => function ($row) {
                return $row['name']->getUniqueName();
            },
            'field' => 'a.name',
        ])->addColumn([
                'label' => 'entity.Document.list.thead.uniqueName',
                'formatter' => function ($row) {
                    return $row['uniqueName']->getUniqueName();
                },
                'field' => 'a.uniqueName',
            ])
            ->addColumn([
                'label' => 'entity.Document.list.thead.description',
                'formatter' => function ($row) {
                    return $row['site']->getDescription();
                },
                'field' => 'a.description',
            ])

        ;

        // actions
        $list->addAction([ // edit link
            'displayer' => function ($row, $security) {
                return $security->isGranted('SITE_EDIT', $row['site']);
            },
            'ajax' => false,
            'title' => $this->translator->trans('form.edit'),
            'href' => function ($row) {
                return $this->router->generate('admin.sites.edit', ['id' => $row['id']]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-edit"></i>';
            },
        ])
           ;




        return $list;
    }
}
