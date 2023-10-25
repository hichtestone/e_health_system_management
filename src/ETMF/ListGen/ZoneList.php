<?php

namespace App\ETMF\ListGen;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ETMF\Entity\Zone;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class ZoneList
 * @package App\ETMF\ListGen
 */
class ZoneList extends AbstractListGenType
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
		$repository = $this->em->getRepository(Zone::class);
		$url        = 'app.etmf.zone.ajax';
		$urlArgs    = [];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'zone',
				'alias' => 'zoneObject',
			])
			->addHiddenData([
				'field' => 'zone.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [])
			->setExportFileName('etmf-zone');

		$list
			->addColumn([
				'label' => 'Nom de la zone',
				'field' => 'zone.name',
			])
			->addColumn([
				'label'         => 'Numéro de la zone',
				'formatter'     => function ($row) {
					return $this->threeDigit($row['zoneObject']->getCode());
				},
				'formatter_csv' => function ($row) {
					return $this->threeDigit($row['zoneObject']->getCode());
				},
				'sortField'     => 'zone.code',
			])
			->addColumn([
				'label'         => 'Archivage',
				'formatter'     => function ($row) {
					return null === $row['zoneObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => function ($row) {
					return null === $row['zoneObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'sortField'     => 'zone.deletedAt',
			])
			->addFilter([
				'label'       => 'Nom de la zone',
				'field'       => 'zone.name',
				'selectLabel' => 'zone.name',
			], 'select')
			->addFilter([
				'label'       => 'N° de la zone',
				'field'       => 'zone.code',
				'selectLabel' => 'zone.code',
			], 'select')
			->addFilter([
				'label'              => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field'              => 'zone.deletedAt',
				'defaultValues'      => [0],
			], 'archived')
			->addAction([
				'href'      => function ($row) {
					if ($this->security->isGranted('ZONE_EDIT', $row['zoneObject'])) {
						return $this->router->generate('app.etmf.zone.edit', ['zoneID' => $row['zoneObject']->getId()]);
					} else {
						return '';
					}
				},
				'formatter' => function ($row) {
					if ($this->security->isGranted('ZONE_EDIT', $row['zoneObject'])) {
						return '<i class="fa fa-edit"></i>';
					} else {
						return '';
					}
				},
			])
			->addAction([
				'href'      => function ($row) {
					if (null === $row['zoneObject']->getDeletedAt()) {
						if ($this->security->isGranted('ZONE_ARCHIVE', $row['zoneObject'])) {
							return $this->router->generate('app.etmf.zone.archive', ['zoneID' => $row['zoneObject']->getId()]);
						} else {
							return '';
						}
					} else {
						return $this->router->generate('app.etmf.zone.restore', ['zoneID' => $row['zoneObject']->getId()]);
					}

				},
				'formatter' => function ($row) {
					if (null === $row['zoneObject']->getDeletedAt()) {
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
