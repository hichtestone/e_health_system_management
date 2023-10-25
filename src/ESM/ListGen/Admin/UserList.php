<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\User;
use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\ListGen;

/**
 * Class UserList.
 */
class UserList extends AbstractListGenType
{
    /**
     * @return ListGen
     */
    public function getList()
    {
        // config globale
        $rep = $this->em->getRepository(User::class);
        $url = 'admin.user.index.ajax';
        $urlArgs = [];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'u',
                'alias' => 'user',
            ])
            ->addHiddenData([
                'field' => 'c',
                'alias' => 'civility',
            ])
            ->addHiddenData([
                'field' => 'u.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen')
            ->setExportFileName('users')
            ->addConstantSort('u.lastName', 'ASC')
        ;

        if ($this->security->isGranted('ROLE_USER_WRITE')) {
            $list->addMultiAction([
                'label' => $this->translator->trans('entity.Project.user.attach_projects'),
                'href' => $this->router->generate('admin.user.attach_mass_popup'),
                'displayer' => function ($security) {
                    return $security->isGranted('ROLE_PROJECT_WRITE');
                },
                'displayerRow' => function ($row) {
                    return null === $row['user']->getDeletedAt();
                },
            ]);
        }
        $list->addAction([ // enable/disable
            'displayer' => function ($row, $security) {
                return $security->isGranted('USER_ACCESS', $row['user']);
            },
            'ajax' => true,
            'afterUpdEffect' => [7],
            'title' => function ($row) {
                if ($row['user']->getHasAccessEsm()) {
                    return $this->translator->trans('form.disable').' accès';
                } else {
                    return $this->translator->trans('form.enable').' accès';
                }
            },
            'href' => function ($row) {
                return $this->router->generate('admin.user.switchEnabled', ['id' => $row['id']]);
            },
            'formatter' => function ($row) {
                if ($row['user']->getHasAccessEsm()) {
                    return '<i class="fa fa-check c-green"></i>';
                } else {
                    return '<i class="fa fa-times c-red"></i>';
                }
            },
        ]);
        $list->setActionLabel('entity.User.field.has_access_esm', 'messages');

        // colonnes
        $list->addColumn([ // Civilité
            'label' => 'entity.User.civility',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['user']->getCivility();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'u.civility',
        ]);

        $list->addColumn([ // Prénom
            'label' => 'entity.User.first_name',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['user']->getFirstName();
            },
            'field' => 'u.firstName',
        ]);

        $list->addColumn([ // Nom
            'label' => 'entity.User.last_name',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return '<a href="'.$this->router->generate('admin.user.show', ['id' => $row['id']]).'">'.$row['user']->getLastName().'</a>';
            },
            'field' => 'u.lastName',
        ]);

        $list->addColumn([ // Profile
            'label' => 'entity.Profile.label',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                if (null == $row['user']->getProfile()) {
                    $s = '';
                } else {
                    $s = $row['user']->getProfile()->getName();
                }
                if (1 == $row['user']->getIsSuperAdmin()) {
                    $s .= ' <span class="badge badge-info">super admin</span>';
                }

                return $s;
            },
            'formatter_csv' => function ($row) {

                return $row['user']->getProfile()->getName();
            },
            'sortField' => 'p.profile',
        ]);

        $list->addColumn([ // Fonction
            'label' => 'entity.User.job',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['user']->getJob();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'u.job',
        ]);

        $list->addColumn([ // Société
            'label' => 'entity.User.society',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['user']->getSociety();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'u.society',
        ]);

        $list->addFilter([
            'label' => 'entity.User.field.name',
            'field' => 'concat(u.firstName, u.lastName)',
        ], 'string')
        ;
        $list->addFilter([
            'label' => 'entity.User.field.profile',
            'field' => 'p.id',
            'selectLabel' => 'p.acronyme',
        ], 'select')
        ;
        $list->addFilter([
            'label' => 'entity.User.field.function',
            'field' => 'j.id',
            'selectLabel' => 'j.label',
        ], 'select')
        ;
        $list->addFilter([
            'label' => 'entity.User.field.has_access_esm',
            'field' => 'u.hasAccessEsm',
            'selectLabel' => function ($item) {
                return $item ? 'Oui' : 'Non';
            },
        ], 'select')
        ;
        if ($this->security->isGranted('ROLE_USER_WRITE')) {
            $list->addColumn([
                'label' => 'entity.status.label',
                'formatter' => function ($row) {
                    return null === $row['user']->getDeletedAt() ? 'Non' : 'Oui';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.deletedAt',
            ]);

            $list->addFilter([
                'label' => 'filter.archived.label',
                'translation_domain' => 'ListGen',
                'field' => 'u.deletedAt',
                'defaultValues' => [0],
            ], 'archived')
            ;
        }

        return $list;
    }
}
