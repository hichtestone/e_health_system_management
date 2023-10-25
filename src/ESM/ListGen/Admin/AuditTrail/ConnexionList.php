<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Entity\ConnectionAuditTrail;
use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\ListGen;

class ConnexionList extends AbstractListGenType
{
    /**
     * @return ListGen
     */
    public function getList()
    {
        // config globale
        $rep = $this->em->getRepository(ConnectionAuditTrail::class);
        $url = 'admin.audit_trail.connexion.ajax';
        $urlArgs = [];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'c',
                'alias' => 'conn',
            ])
            ->addHiddenData([
                'field' => 'c.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen')
            ->setExportFileName('connexion_audit_trail')
            ->addConstantSort('c.started_at', 'DESC')
        ;

        // colonnes
        $list->addColumn([
            'label' => 'entity.User.label',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return $row['conn']->getUser()->getFullName();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'u.lastName',
        ]);

        $list->addColumn([
            'label' => 'entity.field.started_at',
            'formatter' => function ($row) {
                return $row['conn']->getStartedAt()->format('d/m/Y H:i:s');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'c.started_at',
        ]);
        $list->addColumn([
            'label' => 'entity.field.ended_at',
            'formatter' => function ($row) {
                return null === $row['conn']->getEndedAt() ? '' : $row['conn']->getEndedAt()->format('d/m/Y H:i:s');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'c.ended_at',
        ]);

        $list->addFilter([
            'label' => 'entity.User.field.name',
            'field' => 'concat(u.firstName, u.lastName)',
        ], 'string');
        $list->addFilter([
            'label' => 'entity.field.started_at',
            'field' => 'c.started_at',
        ], 'date');

        return $list;
    }
}
