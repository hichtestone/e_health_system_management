<?php

namespace App\ESM\ListGen\Admin;

use App\ESM\Entity\Profile;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class ProfileList.
 */
class ProfileList extends AbstractListGenType
{
    /**
     * @return mixed
     */
    public function getList(string $locale)
    {
        $repository = $this->em->getRepository(Profile::class);

        $url = 'admin.profile.index.ajax';
        $urlArgs = [];

        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData(['id' => 'id'])
            ->addHiddenData([
                'field' => 'p',
                'alias' => 'profile',
            ])
            ->addHiddenData([
                'field' => 'p.id',
            ])
            ->setRepository($repository)
            ->setRepositoryMethod('indexListGen')
            ->setExportFileName('profiles')
            ->addConstantSort('p.name', 'ASC')
        ;

        // colonnes
        $list->addColumn([
            'label' => 'entity.Profile.name',
            'translation_args' => ['%count%' => 1],
            'formatter' => function ($row) {
                return '<a href="'.$this->router->generate('admin.profile.show', ['id' => $row['id']]).'">'.$row['profile']->getName().'</a>';
            },
            'field' => 'p.name',
        ]);

        $list->addColumn([
            'label' => 'entity.Profile.acronyme',
            'translation_args' => ['%count%' => 1],
            'field' => 'p.acronyme',
        ]);

		$list->addColumn([
			'label' => 'entity.Profile.typeProfil',
			'field' => 'p.type',
		]);


		$list->addFilter([
			'label' => 'type',
			'translation_domain' => 'ListGen',
			'field' => 'p.type',
			'defaultValues' => 'ESM',
		], 'type');

        if ($this->security->isGranted('ROLE_PROFILE_WRITE')) {

            $list->addColumn([
                'label' => 'entity.status.label',
                'formatter' => function ($row) {
                    return null === $row['profile']->getDeletedAt() ? 'Non' : 'Oui';
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'p.deletedAt',
            ]);

            $list->addFilter([
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
