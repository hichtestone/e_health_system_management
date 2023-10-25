<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\UserProject;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * liste page intervenants du projet.
 */
class UserProjectList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(Project $project)
    {
        $repository = $this->em->getRepository(UserProject::class);
        $url        = 'project.user.list.ajax';
        $urlArgs    = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                         ->setClass('table')
                         ->setRowData(['id' => 'id'])
                         ->addHiddenData([
                             'field' => 'up',
                             'alias' => 'user_project',
                         ])
                         ->addHiddenData([
                             'field' => 'u.id',
                         ])
                         ->setRepository($repository)
                         ->setRepositoryMethod('indexListGen', ['project' => $project])
                         ->setExportFileName('intevenants')
                         ->addConstantSort('u.lastName', 'ASC')
                         ->addMultiAction([
                             'label'             => 'Créer une réunion',
                             'href'              => $this->router->generate('project.meeting.new', ['id' => $project->getId()]),
                             'displayer'         => function ($security) use ($project) {
                                return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_COMMUNICATION_WRITE');
                             },
                             'displayerRow'      => function ($row) {
                                return null === $row['user_project']->getDisabledAt();
                             },
                             'data' => ['redirect' => 'user_ids'],
                         ])
                         ->addMultiAction([
                             'label'       => 'Créer une formation',
                             'href'        => $this->router->generate('project.training.new', ['id' => $project->getId()]),
                             'displayer'   => function ($security) use ($project) {
                                return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_COMMUNICATION_WRITE');
                             },
                             'displayerRow' => function ($row) {
                                return null === $row['user_project']->getDisabledAt();
                             },
                             'data'         => ['redirect' => 'user_ids'],
                         ])
                         ->addMultiAction([
                             'label'         => 'Créer un contact',
                             'href'          => $this->router->generate('project.contact.new', ['id' => $project->getId(), 'from' => 'intervenant']),
                             'displayer'     => function ($security) use ($project) {
                                return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_COMMUNICATION_WRITE');
                             },
                             'displayerRow'  => function ($row) {
                                return null === $row['user_project']->getDisabledAt();
                             },
                             'data'           => ['redirect' => 'user_ids'],
                         ]);

        // colonnes
        $list->addColumn([ // nom
            'label'         => 'entity.Project.user.intervenant',
            'formatter'     => function ($row) use ($project) {
                $user = $row['user_project']->getUser();
                return '<a href="'.$this->router->generate('project.user.show', ['id' => $project->getId(), 'idUser' => $row['id']]).'" title="Voir la fiche">'.$user->getFullName().'</a>';
            },
            'formatter_csv' => function ($row) {
                $user = $row['user_project']->getUser();
                return $user->getFullName();
            },
            'sortField'     => 'u.lastName',
        ]);

        $list->addColumn([ // access ESM
            'label'         => 'entity.User.hasAccesEsm',
            'formatter'     => function ($row) {
                $user = $row['user_project']->getUser();
                return $user->getHasAccessESM() ? 'Oui' : 'Non';
            },
            'formatter_csv' => 'formatter',
            'sortField'     => 'u.hasAccessEsm',
        ]);

        $list->addColumn([ // Profile
            'label'            => 'entity.Profile.label',
            'translation_args' => ['%count%' => 1],
            'formatter'        => function ($row) {
                if (null == $row['user_project']->getUser()->getProfile()) {
                    $s = '';
                } else {
                    $s = $row['user_project']->getUser()->getProfile()->getName();
                }
                if (1 == $row['user_project']->getUser()->getIsSuperAdmin()) {
                    $s .= ' <span class="badge badge-info">super admin</span>';
                }
                return $s;
            },
            'formatter_csv'  => function ($row) {
                    return $row['user_project']->getUser()->getProfile()->getName();
            },
            'sortField'     => 'u.profile',
        ]);

        $list->addColumn([ // Fonction
            'label'            => 'entity.User.job',
            'translation_args' => ['%count%' => 1],
            'formatter'        => function ($row) {
                return $row['user_project']->getUser()->getJob();
            },
            'formatter_csv'    => 'formatter',
            'sortField'        => 'u.job',
        ]);

        $list->addColumn([ // enabled at
            'label'         => 'entity.Project.user.enabledAt',
            'formatter'     => function ($row) {
                return null === $row['user_project']->getEnabledAt() ? '' : $row['user_project']->getEnabledAt()->format('d/m/Y');
            },
            'formatter_csv' => 'formatter',
            'sortField'     => 'up.enabledAt',
        ]);

        $list->addColumn([ // disabled at
            'label'         => 'entity.Project.user.disabledAt',
            'formatter'     => function ($row) {
                 return null === $row['user_project']->getDisabledAt() ? '' : $row['user_project']->getDisabledAt()->format('d/m/Y');
            },
            'formatter_csv' => 'formatter',
            'sortField'     => 'u.disabledAt',
        ]);

        $list->addFilter([
            'label' => 'entity.Participant.project.name',
            'field' => 'CONCAT(u.firstName,u.lastName)',
        ], 'string')
        ;

        $list->addFilter([
            'label' => 'entity.status.disabled',
            'field' => 'up.disabledAt',
        ], 'archived')
        ;

        return $list;
    }
}
