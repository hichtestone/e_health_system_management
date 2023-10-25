<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Entity\ConnectionErrorAuditTrail;
use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\ListGen;

class ConnexionErrorList extends AbstractListGenType
{
    /**
     * @return ListGen
     */
    public function getList()
    {
        // config globale
        $rep = $this->em->getRepository(ConnectionErrorAuditTrail::class);
        $url = 'admin.audit_trail.connexion_error.ajax';
        $urlArgs = [];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'e',
                'alias' => 'err',
            ])
            ->addHiddenData([
                'field' => 'e.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen')
            ->setExportFileName('connexion_error_audit_trail')
            ->addConstantSort('e.created_at', 'DESC')
        ;

        // colonnes
        $list->addColumn([
            'label' => 'entity.User.label',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                $user = $row['err']->getUser();
                if (null === $user) {
                    return 'Unknown';
                } else {
                    return $user->getFullName();
                }
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'u.lastName',
        ]);

        $list->addColumn([
            'label' => 'entity.field.created_at',
            'formatter' => function ($row) {
                return $row['err']->getCreatedAt()->format('d/m/Y H:i:s');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'e.created_at',
        ]);
        $list->addColumn([
            'label' => 'entity.field.comment',
            'formatter' => function ($row) {
                return $this->translator->trans($row['err']->getError());
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'e.error',
        ]);

        $list->addFilter([
            'label' => 'entity.User.field.name',
            'field' => 'concat(u.firstName, u.lastName)',
        ], 'string');
        $list->addFilter([
            'label' => 'entity.field.created_at',
            'field' => 'e.created_at',
        ], 'date');

        return $list;
    }
}
