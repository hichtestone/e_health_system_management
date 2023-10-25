<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\DocumentTransverse;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class DrugList.
 */
class DocumentTransverseList extends AbstractListGenType
{
    /**
     * @return mixed
     *
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    public function getList()
    {
        $repository = $this->em->getRepository(DocumentTransverse::class);
        $url = 'document.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'i',
                'alias' => 'documentTransverse',
            ])
            ->addHiddenData([
                'field' => 'i.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGen')
            ->setExportFileName('documentTransverse')
            ->addConstantSort('i.name', 'ASC')
        ;

        $list
            ->addColumn([ //name
                'label' => 'entity.DocumentTransverse.field.name',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return '<a href="'.$this->router->generate('document.show',
                            ['id' => $row['documentTransverse']->getId()]).'">'.$row['documentTransverse']->getName().'</a>';
                },
                'sortField' => 'i.name',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTransverse.field.porteeDocument',
                'formatter' => function ($row) {
                    return $row['documentTransverse']->getPorteeDocument();
                },
                'sortField' => 'i.porteeDocument',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTransverse.field.TypeDocument',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) {
                    return $row['documentTransverse']->getTypeDocument();
                },
                'sortField' => 'i.TypeDocument',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTransverse.field.drug',
                'formatter' => function ($row) {
                    $returnDrug = $row['documentTransverse']->getDrug();
                    $returnInstitution = $row['documentTransverse']->getInstitution();
                    $returnInterlocutor = $row['documentTransverse']->getInterlocutor();

                    return null === $returnDrug ? (null === $returnInstitution ? (null === $returnInterlocutor ? '' : $returnInterlocutor) : $returnInstitution) : $returnDrug;
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'i.drug',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTransverse.field.validStartAt',
                'formatter' => function ($row) {
                    return $row['documentTransverse']->getValidStartAt()->format('d/m/Y');
                },
                'sortField' => 'i.validStartAt',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTransverse.field.validEndAt',
                'formatter' => function ($row) {
                    return $row['documentTransverse']->getValidEndAt()->format('d/m/Y');
                },
                'sortField' => 'i.validEndAt',
            ])
            ->addColumn([ //status de validité
                'label' => 'entity.DocumentTransverse.field.isValid',
                'formatter' => function ($row) {
                    return $row['documentTransverse']->getIsValid() ? ' Valide' : 'Invalide';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'i.IsValid',
            ])
        ->addAction([
            'href' => function ($row) {
                return $this->router->generate('admin.drug.show', ['id' => $row['documentTransverse']->getId()]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-download "></i>';
            },
        ])
            ->addFilter([
                'label' => 'filter.archived.label',
                'translation_domain' => 'ListGen',
                'field' => 'i.deletedAt',
                'defaultValues' => [0],
            ], 'archived')

           ;

        return $list;
    }
}
