<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class DocumentList extends AbstractListGenType
{
    use AuditTrailTrait;

	/**
	 * @param string $category
	 * @return ListGen
	 */
    public function getList(string $category): ListGen
	{
        // config globale
        $rep = $this->em->getRepository('App\\ETMF\\Entity\\AuditTrail\\'.$this->getCamelCaseCategory($category.'_audit_trail'));
        $url = 'admin.audit_trail.generic.index.ajax';
        $urlArgs = ['category' => $category];
        $entityTrans = 'entity.'.$this->getCamelCaseCategory($category).'.label';
        $entityTransArgs = ['%count%' => 1];

        $list = $this->setAuditTrailConfigPart1($rep, $url, $urlArgs);
        $list->setExportFileName($category.'_audit_trail');

        $list->addColumn([
            'label' => 'Zone',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getZone();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'zone.name',
        ]);

        $list->addColumn([
            'label' => 'Section',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getSection();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'section.name',
        ]);

        $list->addColumn([
            'label' => 'Artefact',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getArtefact();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'artefact.name',
        ]);

        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);


        return $list;
    }
}
