<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\CourbeSetting;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class CourbeSettingList.
 */
class CourbeSettingList extends AbstractListGenType
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
        // config globale
        $rep = $this->em->getRepository(CourbeSetting::class);
        $url = 'project.courbe.setting.index.ajax';
        $urlArgs = ['id' => $project->getId()];
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'p',
                'alias' => 'courbe',
            ])
            ->addHiddenData([
                'field' => 'p.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('indexListGen', [$project])
            ->setExportFileName('courbes')

            ->addConstantSort('p.unit', 'ASC')
        ;
        $list->addColumn([ // id courbe
            'label' => 'id',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                $return = $row['courbe']->getId();
                $return = $row['courbe']->getId();

                return '<a href="'.$this->router->generate('admin.project.show', ['id' => $row['id']]).'"
                 title="Voir fiche">'.$return.'</a>';

                return is_null($return) ? ' - ' : $return;
            },
            'sortField' => 'p.id',
        ]);
        $list

            ->addColumn([ //startAt date de debut de la courbe
                'label' => 'Date de debut de la courbe',
                'formatter' => function ($row) {
                    $return = $row['courbe']->getDatestrat();

                    return is_null($return) ? ' - ' : $return->format('d/m/Y');
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.datestrat',
            ]);
        $list->addColumn([ // unit
            'label' => 'Unit',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                $return = $row['courbe']->getUnit();

                return is_null($return) ? ' - ' : $return;
            },
            'sortField' => 'p.unit',
        ]);

        /*   $list->addColumn([ // Nbre de points
               'label' => 'Nbre de point',
               'translation_args' => ['%count%' => 1],
               'formatter' => function ($row) {
                   return $row['courbe']->getnbrepoints();
               },
               'sortField' => 'p.points',
           ]);*/

        $list->addFilter([
               'label' => 'Date de debut',
               'field' => 'p.datestrat',
           ], 'dates')
           ;

        $list->addAction([
                'href' => function ($row) use ($project) {
                    return $this->router->generate('project.list.courbe.edit', ['project' => $project->getId(),
                        'courbe' => $row['id'], ]);
                },
                'formatter' => function ($row) {
                    return '<i class="fa fa-edit"></i>';
                },
            ]);
        $list->addAction([
            'href' => function ($row) use ($project) {
                return $this->router->generate('project.list.courbe.show', ['project' => $project->getId(), 'courbe' => $row['id']]);
            },
            'formatter' => function ($row) {
                return '<i class="fa fa-eye c-grey"></i>';
            },
        ]);

        return $list;
    }
}
