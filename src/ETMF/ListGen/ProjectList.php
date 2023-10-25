<?php

namespace App\ETMF\ListGen;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\ListGen;

/**
 * Class StudyList.
 */
class ProjectList extends AbstractListGenType
{
    public function getList(): ListGen
    {
        // config globale
        $this->setListGen();

        // columns
        $this->setColumns();

        // actions
        $this->setActions();

        // filters
        $this->setFilters();

        return $this->lg;
    }

    private function getEntity(array $row): Study
    {
        return $row['study'];
    }

    private function setListGen(): void
    {
        // TODO Dependency-Injection
        $locale = 'fr';

        $rep = $this->em->getRepository(Study::class);
        $urlArgs = [];

        $this->lg
            ->setAjaxUrl($this->router->generate('admin.studies.index.ajax', $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 's',
                'alias' => 'study',
            ])
            ->addHiddenData([
                'field' => 's.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen', ['locale' => $locale])
        ;
    }

    private function setColumns(): void
    {
        $this->lg
            ->addColumn([
                'label' => $this->translator->trans('entity.Study.label', ['%count%' => 1]),
                'formatter' => function ($row) {
                    return '<a href="'.$this->router->generate('admin.studies.show', ['id' => $row['id']]).'">'.$this->getEntity($row)->getName().'</a>';
                },
                'field' => 's.name',
                'formatter_csv' => 'name',
            ])
            ->addColumn([
                'label' => $this->translator->trans('entity.Study.form.field.protocol'),
                'formatter' => function ($row) {
                    return $this->getEntity($row)->getProtocol();
                },
                'formatter_csv' => 'formatter',
                'sortField' => 's.protocol',
            ])
            ->addColumn([
                'label' => $this->translator->trans('entity.Study.form.field.storage'),
                'formatter' => function ($row) {
                    return $this->getEntity($row)->getStorage();
                },
                'formatter_csv' => 'formatter',
                'sortField' => 's.storage',
            ])
        ;
    }

    private function setActions(): void
    {
        
    }

    private function setFilters(): void
    {
        $this->lg
            ->addFilter([
                'label' => $this->translator->trans('entity.Study.label', ['%count%' => 1]),
                'field' => 's.name',
            ], 'string')
        ;
    }
}
