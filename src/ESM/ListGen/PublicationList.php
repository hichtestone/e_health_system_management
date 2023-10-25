<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\Publication;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class PublicationList.
 */
class PublicationList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(Publication::class);
        $url        = 'project.publication.index.ajax';
        $urlArgs    = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                        ->setClass('table')
                        ->setRowData(['id' => 'id'])
                        ->addHiddenData([
                            'field' => 'p',
                            'alias' => 'publication',
                        ])
                        ->addHiddenData([
                            'field' => 'p.id',
                        ])
                        ->setRepository($repository)
                        ->setRepositoryMethod('indexListGenProjectPublication', [$project->getId()])
                        ->setExportFileName('publications')
                        ->addConstantSort('p.id', 'ASC');

        $list
            ->addColumn([
                'label' => 'entity.Project.publication.register.labels.communicationType',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($project) {
                    return '<a href="'.$this->router->generate('project.publications.show', ['id' => $project->getId(), 'idPublication' => $row['publication']->getId()]).'">'.$row['publication']->getCommunicationType().'</a>';
                },
                'formatter_csv' => function ($row) {
                    return $row['publication']->getCommunicationType();
                },
                'sortField' => 'p.communicationType',
        ])
            ->addColumn([
                'label' => 'entity.Project.publication.register.labels.isCongress',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($project) {
                    return !is_null($row['publication']->getIsCongress()) ? $row['publication']->getIsCongress() : '';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.isCongress',
            ])
            ->addColumn([
                'label' => 'entity.Project.publication.register.labels.name',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($project) {
                    return !is_null($row['publication']->getJournals()) ? $row['publication']->getJournals() : $row['publication']->getCongress() ?? '';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.id',
            ])
            ->addColumn([
                'label' => 'entity.Project.publication.register.labels.postType',
                'translation_args' => ['%count%' => 1],
                'formatter' => function ($row) use ($project) {
                    return !is_null($row['publication']->getPostType()) ? $row['publication']->getPostType() : '';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.postType',
            ])
            ->addColumn([
                'label' => 'entity.Project.publication.register.labels.reference',
                'translation_args' => ['%count%' => 1],
                'field' => 'p.postNumber',
                'formatter'     => function ($row) {
                    return $row['publication']->getPostNumber();
                },
                'formatter_csv' => 'formatter',
                'sortField'     => 'p.postNumber',
            ])
            ->addColumn([
                'label'            => 'entity.Project.publication.register.labels.postAt',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) use ($project) {
                    if (2 === $row['publication']->getCommunicationType()->getId()) {
                        return $row['publication']->getDate()->format('Y');
                    } else {
                        return $row['publication']->getDate()->format('d/m/Y');
                    }
                },
                'formatter_csv'    => 'formatter',
                'sortField'        => 'p.postAt',
            ])
            ->addColumn([
                'label'         => 'entity.Project.publication.register.labels.comment',
                'field'         => 'p.comment',
                'formatter'     => function ($row) {
                    return $row['publication']->getComment();
                },
                'formatter_csv' => 'formatter',
                'sortField'     => 'p.comment',
            ])
            ->addAction([ // enable/disable
                'href' => function ($row) use ($project) {
                    return $this->router->generate('project.publications.show', ['id' => $project->getId(), 'idPublication' => $row['publication']->getId()]);
                },
                'formatter' => function ($row) {
                    return '<i class="fa fa-eye c-grey"></i>';
                },
            ])
            ->addFilter([
                'label' => 'entity.Project.publication.register.labels.name',
                'field' => 'c.id',
                'selectLabel' => 'c.label',
            ], 'select');

        if ($this->security->isGranted('ROLE_PUBLICATION_ARCHIVE')) {
            $list
                ->addColumn([
                    'label' => 'entity.status.label',
                    'formatter' => function ($row) {
                        return null === $row['publication']->getDeletedAt() ? 'Non archivé' : 'Archivé';
                    },
                    'formatter_csv' => 'formatter',
                    'sortField' => 'p.deletedAt',
                ])
                ->addFilter([
                    'label' => 'filter.archived.label',
                    'translation_domain' => 'ListGen',
                    'field' => 'p.deletedAt',
                    'defaultValues' => [0],
                ], 'archived')
            ;
        }

        return $list;
    }
}
