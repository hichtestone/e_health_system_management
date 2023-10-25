<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\VisitPatient;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class VisitPatientList.
 */
class VisitPatientList extends AbstractListGenType
{
    /**
     * @return mixed
     *
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    public function getList(Project $project)
    {
        $repository = $this->em->getRepository(VisitPatient::class);
        $url = 'project.visit.index.ajax';
        $urlArgs = ['id' => $project->getId()];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'vp',
                'alias' => 'visit',
            ])
            ->addHiddenData([
                'field' => 'vp.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGen', [$project])
            ->setExportFileName('visit-patients')
            ->addConstantSort('vp.id', 'ASC')
            ->addMultiAction([
                'label' => 'Modifier le statut',
                'href' => $this->router->generate('project.visitPatient.attach_mass_popup', ['id' => $project->getId()]),
                'displayer' => function ($security) use ($project) {
                    return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_PATIENTTRACKING_WRITE');
                },
            ]);

        $list
            ->addColumn([
                'label' => 'entity.VisitPatient.field.center_number',
                'field' => 'c.number',
            ])
           ->addColumn([
                'label' => 'entity.VisitPatient.field.patient_number',
                'field' => 'p.patient_number',
            ])
           ->addColumn([
                'label' => 'entity.VisitPatient.field.visit',
               'formatter' => function ($row) {
                   return $row['visit']->getVisit();
               },
                'sortField' => 'vp.visit',
            ])
             ->addColumn([
                'label' => 'entity.VisitPatient.field.monitoredAt',
                'formatter' => function ($row) use ($project) {
                    return null === $row['visit']->getMonitoredAt() ? '' : $row['visit']->getMonitoredAt()->format('d/m/Y');
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'vp.monitoredAt',
            ])
            ->addColumn([
                'label' => 'entity.VisitPatient.field.occuredAt',
                'formatter' => function ($row) use ($project) {
                    return null === $row['visit']->getOccuredAt() ? '' : $row['visit']->getOccuredAt()->format('d/m/Y');
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'vp.occuredAt',
            ])
            ->addColumn([
                'label' => 'entity.VisitPatient.field.status',
                'formatter' => function ($row) {
                    return $row['visit']->getStatus();
                },
                'sortField' => 's.label',
            ])
            ->addFilter([
                'label' => 'entity.VisitPatient.field.center_number',
                'field' => 'c.id',
                'selectLabel' => 'c.number',
            ], 'select')
            ->addFilter([
               'label' => 'entity.VisitPatient.field.patient_number',
               'field' => 'p.id',
               'selectLabel' => 'p.patient_number',
            ], 'select')
           ->addFilter([
                'label' => 'entity.VisitPatient.field.visit',
                'field' => 'v.label',
            ], 'select')
            ->addFilter([
                'label' => 'entity.VisitPatient.field.monitoredAt',
                'field' => 'vp.monitoredAt',
            ], 'dates')
            ->addFilter([
                'label' => 'entity.VisitPatient.field.occuredAt',
                'field' => 'vp.occuredAt',
            ], 'dates')
            ->addFilter([
                'label' => 'entity.VisitPatient.field.status',
                'field' => 's.label',
            ], 'select')
        ;

        return $list;
    }
}
