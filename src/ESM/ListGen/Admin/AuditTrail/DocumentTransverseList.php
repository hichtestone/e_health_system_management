<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;

class DocumentTransverseList extends AbstractListGenType
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
            'label' => 'entity.DocumentTransverse.field.porteeDocument',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getPorteeDocument();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'j.porteeDocument',
        ]);

        $list->addColumn([
            'label' => 'entity.DocumentTransverse.field.drug',
            'formatter' => function ($row) {
                $returnDrug = $row['audit_trail']->getEntity()->getDrug();
                $returnInstitution = $row['audit_trail']->getEntity()->getInstitution();
                $returnInterlocutor = $row['audit_trail']->getEntity()->getInterlocutor();
                if (null == $returnDrug and null == $returnInstitution) {
                    return $Result = '<a href="'.$this->router->generate('admin.interlocutor.show', ['id' => $row['id']]).'">'.$returnInterlocutor.'</a>';
                } elseif (null == $returnDrug and null == $returnInterlocutor) {
                    return $Result = '<a href="'.$this->router->generate('admin.institution.show', ['id' => $row['id']]).'">'.$returnInstitution.'</a>';
                } elseif (null == $returnInstitution and null == $returnInterlocutor) {
                    return $Result = '<a href="'.$this->router->generate('admin.drug.show', ['id' => $row['id']]).'">'.$returnDrug.'</a>';
                }
                return true;
            },

            'formatter_csv' => 'formatter',
            'sortField' => 'j.drug',
        ]);

        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
