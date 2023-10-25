<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Drug;
use App\ESM\Entity\ProjectTrailTreatment;
use App\ESM\Service\ListGen\AbstractListGenType;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DrugList.
 */
class DrugList extends AbstractListGenType
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
        $repository = $this->em->getRepository(Drug::class);
        $url 		= 'admin.drugs.index.ajax';
        $urlArgs 	= [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			 ->setClass('table')
			 ->setRowData(['id' => 'id'])
			 ->addHiddenData([
				 'field' => 'i',
				 'alias' => 'drug',
			 ])
			 ->addHiddenData([
				 'field' => 'i.id',
			 ])
			 ->setRepository($repository)
			 ->setRepositoryMethod('indexListGen', [$displayArchived])
			 ->setExportFileName('drugs')
			 ->addConstantSort('i.name', 'ASC');

        $list
            ->addColumn([ //name
                'label'            => 'entity.Drug.field.name',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return '<a href="'.$this->router->generate('admin.drug.show', ['id' => $row['drug']->getId()]).'">'.$row['drug']->getName().'</a>';
                },
                'formatter_csv'    => function ($row) {
                    return $row['drug']->getName();
                },
                'sortField'        => 'i.name',
            ])
            ->addColumn([ //type de traitement
                'label'            => 'entity.Drug.field.TreatmentType',
                'translation_args' => ['%count%' => 1],
                'formatter'        => function ($row) {
                    return $row['drug']->getTrailTreatment();
                },
                'formatter_csv'   => 'formatter',
                'sortField'       => 'i.TrailTreatment',
            ])
            ->addColumn([ //status de validité
                'label'         => 'entity.Drug.field.isValid',
                'formatter'     => function ($row) {
                    return $row['drug']->getIsValid() ? ' Valide' : 'Invalide';
                },
                'formatter_csv' => 'formatter',
                'sortField'     => 'i.IsValid',
            ])
            ->addAction([
                'href' => function ($row) {
                    return $this->router->generate('admin.drug.show', ['id' => $row['drug']->getId()]);
                },
                'formatter' => function ($row) {
                    return '<i class="fa fa-eye c-grey"></i>';
                },
            ]);

        if ($this->security->isGranted('ROLE_DRUG_WRITE') and $this->security->isGranted('ROLE_DRUG_READ')) {
            $list->addAction([
                'href' => function ($row) {
                    return $this->router->generate('admin.drug.edit', ['id' => $row['drug']->getId()]);
                },
                'formatter' => function ($row) {
                    if (null !== $row['drug']->getDeletedAt()) {
                        return '';
                    }
                    return '<i class="fa fa-edit"></i>';
                },
            ]);
        }

        if ($this->security->isGranted('ROLE_DRUG_ARCHIVE')) {
            $list->addAction([
                'href' => function ($row) {
                    if (0 === count($row['drug']->getProjects()) && null === $row['drug']->getDeletedAt()) {
                        if ($this->security->isGranted('ROLE_DRUG_ARCHIVE', $row['drug'])) {
                            return $this->router->generate('admin.drug.archive', ['id' => $row['id']]);
                        } else {
                            return '';
                        }
                    } elseif (null !== $row['drug']->getDeletedAt()) {
                        return $this->router->generate('admin.drug.restore', ['id' => $row['id']]);
                    }
                    return '';
                },
                'formatter' => function ($row) {
                    $treatment_entities = $this->em->getRepository(ProjectTrailTreatment::class)->findOneBy(['drug' => $row['drug']]);

                    if (null !== $treatment_entities) {
                        return '';
                    }

                    if (count($row['drug']->getProjects()) > 0) {
                        return '<i class="fa fa-archive c-grey" data-placement="left" data-toggle="tooltip" ></i>';
                    }

                    if (null === $row['drug']->getDeletedAt()) {
                        if ($this->security->isGranted('DRUG_ARCHIVE', $row['drug'])) {
                            return '<i class="fa fa-archive"></i>';
                        }
                    } elseif (null !== $row['drug']->getDeletedAt()) {
                        return '<i class="fa fa-box-open"></i>';
                    }

                    return '';
                },
                'title' => function ($row) {
                    if (count($row['drug']->getProjects()) > 0) {
                        return $this->translator->trans('general.default');
                    }

                    if (null === $row['drug']->getDeletedAt()) {
                        return $this->translator->trans('general.archive');
                    } elseif (null !== $row['drug']->getDeletedAt()) {
                        return $this->translator->trans('general.unarchive');
                    } else {
                    	return '';
                    }
                },
            ])
                ->addColumn([
                    'label'          => 'Archivage',
                    'formatter'      => function ($row) use ($translator) {
                        return null === $row['drug']->getDeletedAt() ? $translator->trans('word.no') : $translator->trans('word.yes');
                    },
                    'formatter_csv'   => 'formatter',
                    'sortField'       => 'i.deletedAt',
                ])
                ->addFilter([
                    'label'              => 'filter.archived.label',
                    'translation_domain' => 'ListGen',
                    'field'              => 'i.deletedAt',
                    'defaultValues'      => [0],
                ], 'archived')
                ->addFilter([
                    'label'       => 'entity.Drug.field.name',
                    'field'       => 'i.id',
                    'selectLabel' => 'i.name',
                ], 'select')
            ;
        }

        return $list;
    }
}
