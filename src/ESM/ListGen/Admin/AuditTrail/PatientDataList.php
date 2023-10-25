<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class PatientDataList extends AbstractListGenType
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
            'label' => 'entity.PatientTracking.field.project',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getPatient()->getProject();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'project.name',
        ]);

		$list->addColumn([
			'label' => 'Variable',
			'formatter' => function ($row) {
				return $row['audit_trail']->getEntity()->getVariable();
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'v.label',
		]);

		$list->addColumn([
			'label' => 'Numéro du centre',
			'formatter' => function ($row) {
				return $row['audit_trail']->getEntity()->getPatient()->getCenter();
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'c.name',
		]);


        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
