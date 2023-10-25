<?php

namespace App\ETMF\ListGen;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ETMF\Entity\Tag;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class TagList
 * @package App\ETMF\ListGen
 */
class TagList extends AbstractListGenType
{
	/**
	 * @return mixed
	 *
	 * @throws InvalidParameterException
	 * @throws MissingMandatoryParametersException
	 * @throws RouteNotFoundException
	 */
	public function getList()
	{
		$repository = $this->em->getRepository(Tag::class);
		$url        = 'app.etmf.tag.ajax';
		$urlArgs    = [];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'tag',
				'alias' => 'tagObject',
			])
			->addHiddenData([
				'field' => 'tag.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [])
			->setExportFileName('etmf-tags');

		$list
			->addColumn([
				'label' => 'Nom du tag',
				'field' => 'tag.name',
			])

			->addColumn([
				'label'         => 'Archivage',
				'formatter'     => function ($row) {
					return null === $row['tagObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => function ($row) {
					return null === $row['tagObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'sortField'     => 't.deletedAt',
			])

			->addColumn([
				'label' => 'Créé le ',
				'field' => 'tag.createdAt',
				'formatter' => function ($row) {
					return null !== $row['createdAt'] ? $row['createdAt']->format('d/m/Y') : '';
				},
				'formatter_csv' => function ($row) {
					return null !== $row['createdAt'] ? $row['createdAt']->format('d/m/Y') : '';
				},
			])

			->addColumn([
				'label' => 'Créé par ',
				'formatter' => function ($row) {
					return $row['tagObject']->getCreatedBy() ? $row['tagObject']->getCreatedBy()->getFirstName() . ' ' . $row['tagObject']->getCreatedBy()->getLastName() : '';
				},
				'formatter_csv' => function ($row) {
                    return $row['tagObject']->getCreatedBy() ? $row['tagObject']->getCreatedBy()->getFirstName() . ' ' . $row['tagObject']->getCreatedBy()->getLastName() : '';
				},
				'sortField'     => 't.createdBy',
			])

			->addFilter([
				'label'       => 'Créé le',
				'field'       => 'tag.createdAt',
			], 'dates')

			->addFilter([
				'label'              => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field'              => 'tag.deletedAt',
				'defaultValues'      => [0],
			], 'archived')

			->addAction([
				'href'      => function ($row) {
					if ($this->security->isGranted('TAG_EDIT', $row['tagObject'])) {
						return $this->router->generate('app.etmf.tag.edit', ['tagID' => $row['tagObject']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) {
					if ($this->security->isGranted('TAG_EDIT', $row['tagObject'])) {
						return '<i class="fa fa-edit"></i>';
					} else {
						return '';
					}
				},
			])

			->addAction([
				'href'      => function ($row) {
					if (null === $row['tagObject']->getDeletedAt()) {
						if ($this->security->isGranted('TAG_ARCHIVE', $row['tagObject'])) {
							return $this->router->generate('app.etmf.tag.archive', ['tagID' => $row['tagObject']->getId()]);
						} else {
							return '';
						}
					} else {
						return $this->router->generate('app.etmf.tag.restore', ['tagID' => $row['tagObject']->getId()]);
					}

				},
				'formatter' => function ($row) {
					if (null === $row['tagObject']->getDeletedAt()) {
						if ($this->security->isGranted('TAG_ARCHIVE', $row['tagObject'])) {
							return '<i class="fa fa-archive"></i>';
						} else {
							return '<i class="fa fa-archive disabled" href="#" data-placement="left" data-toggle="tooltip" title="Impossible d\'archiver le tag"></i>';
						}
					} else {
						return '<i class="fa fa-box-open"></i>';
					}
				},
			]);

		return $list;
	}
}
