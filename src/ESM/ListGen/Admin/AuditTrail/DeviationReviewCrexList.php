<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class DeviationReviewCrexList extends AbstractListGenType
{
    use AuditTrailTrait;

    /**
     * @return ListGen
     */
    public function getList(string $category)
    {
        $rep = $this->em->getRepository('App\\ESM\\Entity\\AuditTrail\\'.$this->getCamelCaseCategory($category.'_audit_trail'));
        $url = 'admin.audit_trail.generic.index.ajax';
        $urlArgs = ['category' => $category];
        $entityTrans = 'entity.'.$this->getCamelCaseCategory($category).'.label';
        $entityTransArgs = ['%count%' => 1];

        $list = $this->setAuditTrailConfigPart1($rep, $url, $urlArgs);
        $list->setExportFileName($category.'_audit_trail');

        $list->addColumn([
            'label' => 'Projet',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getDeviation()->getProject();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.name',
        ]);

        $list->addColumn([
            'label' => 'Déviation',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getDeviation();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'd.code',
        ]);

        $list->addColumn([
            'label' => 'Revue CREX',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getNumber();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'd.number',
        ]);

        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
