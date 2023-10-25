<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class ProjectList.
 */
class ProjectList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList($user, string $locale)
    {
        $repository = $this->em->getRepository(Project::class);
        $url = 'admin.project.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'p',
                'alias' => 'project',
            ])
            ->addHiddenData([
                'field' => 'p.id',
            ])
            ->setRepository($repository)
            ->setExportFileName('projects')

            ->setRepositoryMethod('indexListGen')
            ->addConstantSort('p.name', 'ASC')
        ;

        $list->addColumn([
            'label' => 'entity.Project.field.acronyme',
            'formatter' => function ($row) {
                return '<a href="'.$this->router->generate('admin.project.show', ['id' => $row['id']]).'"
                 title="Voir fiche">'.$row['project']->getAcronyme().'</a>';
            },
            'field' => 'p.acronyme',
            'alias' => 'projectName',
        ]);
        $list->addColumn([
            'label' => 'entity.Project.field.sponsor',
            'field' => 's.label',
            'alias' => 'sponsorLabel',
        ]);
        $list->addColumn([
            'label' => 'entity.Project.field.responsible_pm',
            'formatter' => function ($row) {
                $user = $row['project']->getResponsiblePM();

                return '<a href="'.$this->router->generate('admin.user.show', ['id' => $user->getId()]).'"
                 title="Voir fiche">'.$user->getFullName().'</a>';
            },
            'formatter_csv' => function ($row) {
                $user = $row['project']->getResponsiblePM();

                return $user->getFullName();
            },
            'sortField' => 'r.lastName',
        ]);
        $list->addColumn([
            'label' => 'entity.field.created_at',
            'formatter' => function ($row) {
                return $row['project']->getCreatedAt()->format('d/m/Y');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.createdAt',
        ]);
        $list->addColumn([
            'label' => 'entity.field.closed_at',
            'formatter' => function ($row) {
                return null === $row['project']->getClosedAt() ? '' : $row['project']->getClosedAt()->format('d/m/Y');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.closedAt',
        ]);

        $list->addFilter([
            'label' => 'entity.Project.field.name',
            'field' => 'p.name',
        ], 'string')
        ;
        $list->addFilter([
            'label' => 'entity.Project.field.sponsor',
            'field' => 's.id',
            'selectLabel' => 's.label',
        ], 'select')
        ;
        $list->addFilter([
            'label' => 'entity.status.closed',
            'field' => 'p.closedAt',
        ], 'archived')
        ;

        return $list;
    }
}
