<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class DocumentTrackingList.
 */
class DocumentTrackingList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale, Project $project)
    {
        $repository = $this->em->getRepository(DocumentTracking::class);
        $url = 'project.documentTracking.index.ajax';
        $urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'dt',
                'alias' => 'documentTracking',
            ])
            ->addHiddenData([
                'field' => 'dt.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGen', [$project])
            ->setExportFileName('document_tracking')
        ;
        if ($this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_WRITE') || $this->security->isGranted('ROLE_CENTER_WRITE')) {
            $list->addMultiAction([
                'label' => 'Emettre les documents',
                'href' => $this->router->generate('project.documentTracking.mass_popup', ['id' => $project->getId()]),
                'displayerRow' => function ($row) {
                    return null === $row['documentTracking']->getDisabledAt()
                        && $row['documentTracking']->isToBeSent()
                        && ((
                            $this->security->isGranted('ROLE_PROJECT_INTERLOCUTOR_WRITE')
                            && DocumentTracking::levelInterlocutor === $row['documentTracking']->getLevel()
                        ) || (
                            $this->security->isGranted('ROLE_CENTER_WRITE')
                            && DocumentTracking::levelCenter === $row['documentTracking']->getLevel()
                        ));
                },
            ]);
        }

        $list
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.name',
                'formatter' => function ($row) use ($project) {
                    return '<a href="'.$this->router->generate('project.documentTracking.show', ['id' => $project->getId(), 'idDocumentTracking' => $row['documentTracking']->getId()]).'">'.$row['documentTracking']->getTitle().'</a>';
                },
                'field' => 'dt.title',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.version',
                'field' => 'dt.version',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.country',
                'field' => 'c.name',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.scope',
                'formatter' => function ($row) {
                    return $row['documentTracking']->isCenter() ? 'Centre' : 'Interlocuteur';
                },
                'formatter_csv' => 'formatter',
                'field' => 'dt.level',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.toBeSent',
                'formatter' => function ($row) {
                    return $row['documentTracking']->isToBeSent() ? 'Oui' : 'Non';
                },
                'formatter_csv' => 'formatter',
                'field' => 'dt.toBeSent',
            ])
            ->addColumn([
                'label' => 'entity.DocumentTracking.field.toBeReceived',
                'formatter' => function ($row) {
                    return $row['documentTracking']->isToBeReceived() ? 'Oui' : 'Non';
                },
                'formatter_csv' => 'formatter',
                'field' => 'dt.toBeReceived',
            ])
            ;
        /*if ($this->security->isGranted('ROLE_DOCUMENTTRACKING_ARCHIVE')) {
            $list->addColumn([
                'label' => 'Archivé',
                'formatter' => function ($row) {
                    return $row['documentTracking']->getDisabledAt() ? 'Oui' : 'Non';
                },
                'formatter_csv' => 'formatter',
                'field' => 'dt.disabledAt',
            ]);
        }*/

        /*->addAction([ // enable/disable
            'displayer' => function ($row, $security) {
                return $security->isGranted('ROLE_DOCUMENTTRACKING_READ');
            },
            'href' => function ($row) use ($project) {
                return $this->router->generate('project.documentTracking.show', ['id' => $project->getId(), 'idDocumentTracking' => $row['documentTracking']->getId()]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-eye c-grey"></i>';
            },
        ])*/
        $list->addFilter([
                'label' => 'entity.DocumentTracking.field.name',
                'field' => 'dt.title',
            ], 'string')
            ->addFilter([
                'label' => 'entity.DocumentTracking.field.country',
                'field' => 'c.id',
                'selectLabel' => 'c.name',
            ], 'select')
                ->addFilter([
                'label' => 'entity.DocumentTracking.field.version',
                'field' => 'dt.version',
                'selectLabel' => 'dt.version',
            ], 'select');

        /*if ($this->security->isGranted('ROLE_DOCUMENTTRACKING_ARCHIVE')) {
            $list->addFilter([
                'label' => 'Archivage',
                'field' => 'dt.disabledAt',
                'defaultValues' => [0],
            ], 'archived');
        }*/

        return $list;
    }
}
