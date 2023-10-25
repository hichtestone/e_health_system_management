<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class DocumentTrackingCenterList extends AbstractListGenType
{
    use AuditTrailTrait;

    /**
     * @return ListGen
     */
    public function getList(string $category)
    {
        // config globale
        $rep = $this->em->getRepository('App\\ESM\\Entity\\AuditTrail\\'.$this->getCamelCaseCategory($category.'_audit_trail'));
        $url = 'admin.audit_trail.generic.index.ajax';
        $urlArgs = ['category' => $category];
        $entityTrans = 'entity.'.$this->getCamelCaseCategory($category).'.label';
        $entityTransArgs = ['%count%' => 1];

        $list = $this->setAuditTrailConfigPart1($rep, $url, $urlArgs);
        $list->setExportFileName($category.'_audit_trail');

        $list->addColumn([
            'label' => 'entity.DocumentTrackingCenter.project',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getCenter()->getProject();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.name',
        ]);

        $list->addColumn([
            'label' => 'entity.DocumentTrackingCenter.center',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getCenter();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'c.name',
        ]);

        $list->addFilter([
            'label' => 'entity.DocumentTrackingCenter.project',
            'field' => 'p.id',
            'selectLabel' => 'p.name',
        ], 'select');

        $list->addFilter([
            'label' => 'entity.DocumentTrackingCenter.center',
            'field' => 'c.id',
            'selectLabel' => 'c.name',
        ], 'select');

        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
