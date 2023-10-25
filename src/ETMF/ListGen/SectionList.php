<?php

namespace App\ETMF\ListGen;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ETMF\Entity\Section;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class SectionList
 * @package App\ETMF\ListGen
 */
class SectionList extends AbstractListGenType
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
		$repository = $this->em->getRepository(Section::class);
		$url        = 'app.etmf.section.ajax';
		$urlArgs    = [];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'section',
				'alias' => 'sectionObject',
			])
			->addHiddenData([
				'field' => 'section.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [])
			->setExportFileName('etmf-section');

		$list
			->addColumn([
				'label'         => 'Zone',
				'formatter'     => function ($row) {
					return $row['sectionObject']->getZone();
				},
				'formatter_csv' => function ($row) {
					return $row['sectionObject']->getZone();
				},
				'sortField'     => 'section.zone',
			])
			->addColumn([
				'label' => 'Nom de la section',
				'field' => 'section.name',
			])
			->addColumn([
				'label'         => 'Numéro de la section',
				'formatter'     => function ($row) {
					return $this->threeDigit($row['sectionObject']->getCode()).'.'.$this->threeDigit($row['sectionObject']->getZone()->getCode());
				},
				'formatter_csv' => function ($row) {
					return $this->threeDigit($row['sectionObject']->getCode()).'.'.$this->threeDigit($row['sectionObject']->getZone()->getCode());
				},
				'sortField'     => 'section.code',
			])
			->addColumn([
				'label'         => 'Archivage',
				'formatter'     => function ($row) {
					return null === $row['sectionObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => function ($row) {
					return null === $row['sectionObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'sortField'     => 'section.deletedAt',
			])
			->addFilter([
				'label'       => 'zone',
				'field'       => 'zone.name',
				'selectLabel' => 'zone.name',
			], 'select')
			->addFilter([
				'label'       => 'Nom de la section',
				'field'       => 'section.name',
				'selectLabel' => 'section.name',
			], 'select')
			->addFilter([
				'label'       => 'N° de la section',
				'field'       => 'section.code',
				'selectLabel' => 'section.code',
			], 'select')
			->addFilter([
				'label'              => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field'              => 'section.deletedAt',
				'defaultValues'      => [0],
			], 'archived')
			->addAction([
				'href'      => function ($row) {
					if ($this->security->isGranted('SECTION_EDIT', $row['sectionObject'])) {
						return $this->router->generate('app.etmf.section.edit', ['sectionID' => $row['sectionObject']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) {
					if ($this->security->isGranted('SECTION_EDIT', $row['sectionObject'])) {
						return '<i class="fa fa-edit"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href' => function ($row) {
					if (null === $row['sectionObject']->getDeletedAt()) {
						if ($this->security->isGranted('SECTION_ARCHIVE', $row['sectionObject'])) {
							return $this->router->generate('app.etmf.section.archive', ['sectionID' => $row['sectionObject']->getId()]);
						} else {
							return '';
						}
					} else {
						return $this->router->generate('app.etmf.section.restore', ['sectionID' => $row['sectionObject']->getId()]);
					}

				},
				'formatter' => function ($row) {
					if (null === $row['sectionObject']->getDeletedAt()) {
						return '<i class="fa fa-archive"></i>';
					} else {
						return '<i class="fa fa-box-open"></i>';
					}
				},
			]);

		return $list;
	}

	/**
	 * @param int $number
	 * @return string
	 */
	private function threeDigit(int $number): string
	{
		return str_pad($number, 2, "0", STR_PAD_LEFT);
	}
}
