<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Center;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class CenterList.
 */
class SelectionList extends AbstractListGenType
{
    /**
     * @return mixed
     *
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    public function getList(Project $project, bool $displayArchived)
    {
        $repository = $this->em->getRepository(Center::class);
        $url = 'project.selection.index.ajax';
        $urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'c',
                'alias' => 'center',
            ])
            ->addHiddenData([
                'field' => 'c.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexSelectionListGen', [$project, $displayArchived])
            ->setExportFileName('centers')
            ->addConstantSort('c.id', 'ASC');

        $list->addMultiAction([
            'label' => 'Changer statut',
            'href' => $this->router->generate('project.selection.attach_mass_popup', ['id' => $project->getId()]),
            'displayer' => function ($security) use ($project) {
                return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_CENTER_WRITE');
            },
            'displayerRow' => function ($row) {
                return null === $row['center']->getDeletedAt();
            },
        ]);

        $list
            ->addColumn([
                'label' => 'entity.Center.label',
                'formatter' => function ($row) use ($project) {
                    return '<a href="'.$this->router->generate('project.selection.show', ['id' => $project->getId(), 'idCenter' => $row['center']->getId()]).'">'.
                        $row['center']->getNumber().' '.$row['center']->getName().'</a>';
                },
                'formatter_csv' => function ($row) {
                    return $row['center']->getNumber().' '.$row['center']->getName();
                },
                'field' => 'c.number',
            ])
            ->addColumn([
                'label' => 'entity.Institution.label',
                'formatter' => function ($row) use ($project) {
                    $arr = array_map(function ($institution) use ($project) {
                        $str = $institution->getName().' '.$institution->getCity().' '.$institution->getAddress1();

                        return '<a href="'.$this->router->generate('project.institution.show', ['id' => $project->getId(), 'idInstitution' => $institution->getId()]).'" data-sw-link="" data-sw-type="information" data-sw-title="'.$str.'">'.$str.'</a>';
                    }, $row['center']->getInstitutions()->toArray());

                    return implode('<br />', $arr);
                },
                'formatter_csv' => function ($row) {
                    $arr = array_map(function ($institution) {
                        return $institution->getName().' '.$institution->getCity().' '.$institution->getAddress1();
                    }, $row['center']->getInstitutions()->toArray());

                    return implode('; ', $arr);
                },
                'field' => 'i.name',
            ])
            ->addColumn([
                'label' => 'entity.Institution.field.country',
                'field' => 'p.name',
            ])
            ->addColumn([
                'label' => 'entity.Center.field.centerStatus',
                'formatter' => function ($row) {
                    return $row['center']->getCenterStatus();
                },
                'field' => 'cs.label',
            ])
            ->addAction([
                'href' => function ($row) use ($project) {
                    return $this->router->generate('project.selection.show', ['id' => $project->getId(), 'idCenter' => $row['center']->getId()]);
                },
                'formatter' => function ($row) {
                    return '<i class="fa fa-eye c-grey"></i>';
                },
            ])
            ->addFilter([
                'label' => 'entity.Center.field.name',
                'field' => 'c.id',
                'selectLabel' => 'c.name',
            ], 'select')
            ->addFilter([
                'label' => 'Statut',
                'field' => 'cs.id',
                'selectLabel' => 'cs.label',
            ], 'select')
        ;

        if ($this->security->isGranted('ROLE_CENTER_ARCHIVE')) {
            $list
                ->addColumn([
                    'label' => 'entity.status.label',
                    'formatter' => function ($row) {
                        return null === $row['center']->getDeletedAt() ? 'Non archivé' : 'Archivé';
                    },
                    'formatter_csv' => 'formatter',
                    'sortField' => 'c.deletedAt',
                ])
                ->addFilter([
                    'label' => 'filter.archived.label',
                    'translation_domain' => 'ListGen',
                    'field' => 'c.deletedAt',
                    'defaultValues' => [0],
                ], 'archived')
            ;
        }

        return $list;
    }
}
