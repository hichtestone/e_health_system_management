<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Interlocutor;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InterlocutorList.
 */
class InterlocutorList extends AbstractListGenType
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
        $repository = $this->em->getRepository(Interlocutor::class);
        $url        = 'admin.interlocutor.index.ajax';
        $urlArgs    = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
                         ->setClass('table')
                         ->setRowData(['id' => 'id'])
                         ->addHiddenData([
                             'field' => 'i',
                             'alias' => 'interlocutor',
                         ])
                         ->addHiddenData([
                             'field' => 'i.id',
                         ])
                         ->setRepository($repository)
                         ->setRepositoryMethod('indexListGen', [$displayArchived])
                         ->setExportFileName('interlocutors')
                         ->addConstantSort('i.lastName', 'ASC');

        if ($this->security->isGranted('ROLE_INSTITUTION_READ') && $this->security->isGranted('ROLE_INTERLOCUTOR_READ') && $this->security->isGranted('ROLE_INTERLOCUTOR_WRITE')) {
            $list->addMultiAction([
                'label'         => 'Rattacher aux établissements',
                'href'          => $this->router->generate('admin.interlocutor.attach_mass_popup'),
                'displayer'     => function ($security) {
                        return $security->isGranted('ROLE_INSTITUTION_READ') && $security->isGranted('ROLE_INTERLOCUTOR_READ') && $security->isGranted('ROLE_INTERLOCUTOR_WRITE');
                },
                'displayerRow'  => function ($row) {
                        return null === $row['interlocutor']->getDeletedAt();
                },
            ]);
        }

        // colonnes
        $list
            ->addColumn([ //civilité
                'label'            => 'entity.Interlocutor.field.civility',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return '<a href="'.$this->router->generate('admin.interlocutor.show',
                            ['id' => $row['interlocutor']->getId()]).'">'.$row['interlocutor']->getCivility().'</a>';
                },
                'formatter_csv'    => function ($row) {
                    return $row['interlocutor']->getCivility();
                },
                'sortField'        => 'i.civility',
            ])
            ->addColumn([ //prénom
                'label'             => 'entity.Interlocutor.field.firstName',
                'translation_args'  => ['%count%' => 1],
                'formatter_csv'     => 'formatter',
                'field'             => 'i.firstName',
            ])
            ->addColumn([ //nom
                'label'            => 'entity.Interlocutor.field.lastName',
                'translation_args' => ['%count%' => 1],
                'formatter_csv'    => function ($row) {
                    return $row['interlocutor']->getLastName();
                },
                'field'             => 'i.lastName',
            ])
            ->addColumn([ //job
                'label'            => 'entity.Interlocutor.field.job',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['interlocutor']->getJob();
                },
                'formatter_csv'     => 'formatter',
                'sortField'         => 'i.email',
            ])
            ->addAction([
                'href'      => function ($row) {
                     return $this->router->generate('admin.interlocutor.show', ['id' => $row['interlocutor']->getId()]);
                },
                'formatter' => function ($row) {
                     return '<i class="fa fa-eye c-grey"></i>';
                },
            ])
            ->addFilter([
                'label' => 'entity.Interlocutor.field.search',
                'field'    => 'CONCAT(i.lastName,i.firstName)',
            ], 'string')
            ->addFilter([
                'label'       => 'entity.Interlocutor.field.lastName',
                'field'       => 'i.id',
                'selectLabel' => 'i.lastName',
            ], 'select')
            ->addFilter([
                'label'       => 'entity.Interlocutor.field.job',
                'field'       => 'j.id',
                'selectLabel' => 'j.label',
            ], 'select')
        ;

        if ($this->security->isGranted('ROLE_INTERLOCUTOR_ARCHIVE')) {
            $list
                ->addColumn([
                    'label'          => 'entity.status.label',
                    'formatter'      => function ($row) use ($translator) {
                         return null === $row['interlocutor']->getDeletedAt() ? $translator->trans('word.no') : $translator->trans('word.yes');
                    },
                    'formatter_csv'  => 'formatter',
                    'sortField'      => 'i.deletedAt',
                ])
                ->addFilter([
                    'label'              => 'filter.archived.label',
                    'translation_domain' => 'ListGen',
                    'field'              => 'i.deletedAt',
                    'defaultValues'      => [0],
                ], 'archived')
            ;
        }

        return $list;
    }
}
