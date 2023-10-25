<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Institution;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InstitutionList.
 */
class InstitutionList extends AbstractListGenType
{
    /**
     * @return mixed
     *
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    public function getList(bool $displayArchived, TranslatorInterface $translator)
    {
        $repository = $this->em->getRepository(Institution::class);
        $url        = 'admin.institutions.index.ajax';
        $urlArgs    = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                         ->setClass('table')
                         ->setRowData(['id' => 'id'])
                         ->addHiddenData([
                             'field' => 'i',
                             'alias' => 'institution',
                         ])
                         ->addHiddenData([
                             'field' => 'i.id',
                         ])
                         ->setRepository($repository)
                         ->setRepositoryMethod('indexListGen', [$displayArchived])
                         ->setExportFileName('institutions')
                         ->addConstantSort('i.name', 'ASC');

        // colonnes
        $list
            ->addColumn([ //name
                'label'            => 'entity.Institution.field.name',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return '<a href="'.$this->router->generate('admin.institution.show',
                            ['id'   => $row['institution']->getId()]).'">'.$row['institution']->getName().'</a>';
                },
                'formatter_csv'     => function ($row) {
                    return $row['institution']->getName();
                },
                'sortField'         => 'i.name',
            ])
            ->addColumn([ //Type d'établissement
                'label'            => 'entity.Institution.field.type',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['institution']->getInstitutionType() ?? '';
                },
                'formatter_csv'    => 'formatter',
                'sortField'        => 'i.institutionType',
            ])
            ->addColumn([ //adresse
                'label'            => 'entity.Institution.field.address1',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['institution']->getAddress1();
                },
                'formatter_csv'    => 'formatter',
                'sortField'        => 'i.address1',
            ])
            ->addColumn([ //ville
                'label'            => 'entity.Institution.field.city',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['institution']->getCity();
                },
                'formatter_csv'     => 'formatter',
                'sortField'         => 'i.city',
            ])
            ->addColumn([ //pays
                'label'            => 'entity.Institution.field.country',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['institution']->getCountry();
                },
                'formatter_csv'     => 'formatter',
                'sortField'         => 'i.country',
            ])
            ->addColumn([ //numéro finess
                'label'            => 'entity.Institution.field.finess',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['institution']->getFiness();
                },
                'formatter_csv'    => 'formatter',
                'sortField'        => 'i.finess',
            ])
            ->addAction([
                'href'      => function ($row) {
                    return $this->router->generate('admin.institution.show', ['id' => $row['institution']->getId()]);
                },
                'formatter' => function ($row) {
                    return '<i class="fa fa-eye c-grey"></i>';
                },
            ])
            ->addFilter([
                'label' => 'entity.Institution.field.search',
                'field' => 'CONCAT(i.name,i.city,i.address1)',
            ], 'string')
            ->addFilter([
                'label'       => 'entity.Institution.field.name',
                'field'       => 'i.id',
                'selectLabel' => 'i.name',
            ], 'select')
            ->addFilter([
                'label'         => 'entity.Institution.field.type',
                'field'         => 't.id',
                'selectLabel'   => 't.label',
            ], 'select');

        if ($this->security->isGranted('ROLE_INSTITUTION_ARCHIVE')) {
            $list->addColumn([
                'label'         => 'entity.status.label',
                'formatter'     => function ($row) use ($translator) {
                    return null === $row['institution']->getDeletedAt() ? $translator->trans('word.no') : $translator->trans('word.yes');
                },
                'formatter_csv' => 'formatter',
                'sortField'     => 'i.deletedAt',
            ]);

            $list->addFilter([
                'label'              => 'filter.archived.label',
                'translation_domain' => 'ListGen',
                'field'              => 'i.deletedAt',
                'defaultValues'      => [0],
            ], 'archived');
        }

        return $list;
    }
}
