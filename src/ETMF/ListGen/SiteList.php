<?php

namespace App\ListGen;

use App\ESM\Entity\Site;
use App\Service\ListGen\AbstractListGenType;
use App\Service\ListGen\ListGen;

/**
 * Class SiteList.
 */
class SiteList extends AbstractListGenType
{
    public function getList(): ListGen
    {
        // config globale
        $rep = $this->em->getRepository(Site::class);
        $url = 'admin.site.index.ajax';
        $urlArgs = [];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'a',
                'alias' => 'site',
            ])
            ->addHiddenData([
                'field' => 'a.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen')
            ->addConstantSort('a.institution', 'ASC')
            ;

        // colonnes
            $list->addColumn([ // nom
                'label' => 'entity.Site.list.thead.institution',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return '<a href="'.$this->router->generate('admin.sites.show', ['id' => $row['id']]).'">'.$row['site']->getInstitution().'</a>';
                },
                'field' => 'a.institution',
            ])
                ->addColumn([
                    'label' => 'entity.Site.list.thead.department',
                    'formatter' => function ($row) {
                        return $row['site']->getDepartment();
                    },
                    'field' => 'a.department',
                ])
                ->addColumn([
                    'label' => 'entity.Site.list.thead.address',
                    'formatter' => function ($row) {
                        return $row['site']->getAddress();
                    },
                    'field' => 'a.address',
                ])
                ->addColumn([
                    'label' => 'entity.Site.list.thead.city',
                    'formatter' => function ($row) {
                        return $row['site']->getCity();
                    },
                    'field' => 'a.city',
                ])
                ->addColumn([
                    'label' => 'entity.Site.list.thead.postal_code',
                    'formatter' => function ($row) {
                        return $row['site']->getPostalCode();
                    },
                    'field' => 'a.postal_code',
                ])
                ->addColumn([
                    'label' => 'entity.Site.list.thead.number',
                    'formatter' => function ($row) {
                        return $row['site']->getNumber();
                    },
                    'field' => 'a.number',
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
                ->addAction([ // enable/disable
                    'displayer' => function ($row, $security) {
                        return null === $row['site']->getDeletedAt() && $security->isGranted('SITE_EDIT', $row['site']);
                    },
                    'ajax' => true,
                    'afterUpdEffect' => [1, 2],
                    'title' => function ($row) {
                        if ($row['site']->isEnabled()) {
                            return $this->translator->trans('form.disable');
                        } else {
                            return $this->translator->trans('form.enable');
                        }
                    },
                    'href' => function ($row) {
                        return $this->router->generate('admin.user.switchEnabled', ['id' => $row['id']]);
                    },
                    'formatter' => function ($row) {
                        if ($row['user']->isEnabled()) {
                            return '<i class="fa fa-check c-green"></i>';
                        } else {
                            return '<i class="fa fa-times c-red"></i>';
                        }
                    },
                ])
            ;

        // filters
        $list->addFilter([
                'label' => 'form.field.placeholder.search',
                'field' => 'a.institution',
                'translation_args' => ['%count%' => 1],
            ], 'string');

        return $list;
    }
}
