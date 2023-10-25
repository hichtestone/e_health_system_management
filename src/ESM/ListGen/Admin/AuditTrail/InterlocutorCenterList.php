<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class InterlocutorCenterList extends AbstractListGenType
{
    use AuditTrailTrait;

	/**
	 * @param string $category
	 * @return ListGen
	 */
    public function getList(string $category): ListGen
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
            'label' => 'entity.Interlocutor.field.project',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getCenter()->getProject();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.name',
        ]);

        $list->addFilter([
            'label' => 'entity.Center.field.project',
            'field' => 'p.id',
            'selectLabel' => 'p.name',
        ], 'select');

        $list->addColumn([
            'label' => 'entity.Interlocutor.field.interlocutor',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getInterlocutor();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'i.name',
        ]);

        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
