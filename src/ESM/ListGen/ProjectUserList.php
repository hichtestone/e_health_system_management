<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\UserProject;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * liste page accueil.
 */
class ProjectUserList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList($user, string $locale)
    {
        $repository = $this->em->getRepository(UserProject::class);

        $url = 'project.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'up',
                'alias' => 'user_project',
            ])
            ->addHiddenData([
                'field' => 'p.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('homeListGen', [$user, $this->security->isGranted('ROLE_PROJECT_READ_CLOSED')])
            ->setExportFileName('my_projects')
            ->addConstantSort('p.name', 'ASC')
        ;
        $list->addColumn([
            'label' => 'entity.Project.field.acronyme',
            'formatter' => function ($row) {
				$project = $row['user_project']->getProject();
				return '<a href="'.$this->router->generate('project.home.index', ['id' => $project->getId()]).'"
                 title="Voir fiche">'.$project->getAcronyme().'</a>';

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
                $project = $row['user_project']->getProject();
                $user = $project->getResponsiblePM();

                return '<a href="'.$this->router->generate('admin.user.show', ['id' => $user->getId()]).'"
                 title="Voir fiche">'.$user->getFullName().'</a>';
            },
            'formatter_csv' => function ($row) {
                $project = $row['user_project']->getProject();
                $user = $project->getResponsiblePM();

                return $user->getFullName();
            },
            'sortField' => 'r.lastName',
        ]);
        $list->addColumn([
            'label' => 'entity.field.created_at',
            'formatter' => function ($row) {
                $project = $row['user_project']->getProject();

                return $project->getCreatedAt()->format('d/m/Y');
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'p.createdAt',
        ]);
        $list->addColumn([
            'label' => 'entity.field.closed_at',
            'formatter' => function ($row) {
                $project = $row['user_project']->getProject();

                return null === $project->getClosedAt() ? '' : $project->getClosedAt()->format('d/m/Y');
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
            'field' => 's.label',
        ], 'select')
        ;

        return $list;
    }
}
