<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class TagList extends AbstractListGenType
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

        $list = $this->setAuditTrailConfig($rep, $url, $urlArgs, $entityTrans, $entityTransArgs);
        $list->setExportFileName($category.'_audit_trail');

        return $list;
    }
}
