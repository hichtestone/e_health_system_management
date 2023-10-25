<?php

namespace App\ETMF\ListGen;

use App\ESM\Service\ListGen\AbstractListGenType;
use App\ETMF\Entity\Artefact;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArtefactList
 * @package App\ETMF\ListGen
 */
class ArtefactList extends AbstractListGenType
{
	/**
	 * @return mixed
	 *
	 * @throws InvalidParameterException
	 * @throws MissingMandatoryParametersException
	 * @throws RouteNotFoundException
	 */
	public function getList(TranslatorInterface $translator)
	{
		$repository = $this->em->getRepository(Artefact::class);
		$url        = 'app.etmf.artefact.ajax';
		$urlArgs    = [];
		$list       = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'artefact',
				'alias' => 'artefactObject',
			])
			->addHiddenData([
				'field' => 'artefact.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGen', [])
			->setExportFileName('etmf-artefact');

		$list
			->addColumn([
				'label'         => 'Section',
				'formatter'     => function ($row) {
					return $row['artefactObject']->getSection();
				},
				'formatter_csv' => function ($row) {
					return $row['artefactObject']->getSection();
				},
				'sortField'     => 'artefact.section',
			])

			->addColumn([
				'label' => 'Nom de l\'artefact',
				'field' => 'artefact.name',
			])

			->addColumn([
				'label'         => 'Numéro de l\'artefact',
				'formatter'     => function ($row) {
					return $this->threeDigit($row['artefactObject']->getCode()) . '.' . $this->threeDigit($row['artefactObject']->getSection()->getCode()) . '.' . $this->threeDigit($row['artefactObject']->getSection()->getZone()->getCode());
				},
				'formatter_csv' => function ($row) {
					return $this->threeDigit($row['artefactObject']->getCode()) . '.' . $this->threeDigit($row['artefactObject']->getSection()->getCode()) . '.' . $this->threeDigit($row['artefactObject']->getSection()->getZone()->getCode());
				},
				'sortField'     => 'artefact.code',
			])

            ->addColumn([
                'label'         => 'Niveau',
                'formatter'     => function ($row) {
                    $arr = array_map(function ($level) {
                        return $level->getLevel();
                    }, $row['artefactObject']->getArtefactLevels()->toArray());

                    return implode(', ', array_filter($arr));
                },
                'formatter_csv' => function ($row) {
                    $arr = array_map(function ($level) {
                        return $level->getLevel();
                    }, $row['artefactObject']->getArtefactLevels()->toArray());

                    return implode('; ', $arr);
                },
                'sortField'     => 'artefact.artefactLevels',
            ])

			->addColumn([
				'label'         => 'Liste de diffusion',
				'formatter'     => function ($row) {
					$arr = array_map(function ($mailgroup) {
						return $mailgroup->getName();
					}, $row['artefactObject']->getMailgroups()->toArray());

					return implode(', ', array_filter($arr));
				},
				'formatter_csv' => function ($row) {
					$arr = array_map(function ($mailgroup) {
						return $mailgroup->getName();
					}, $row['artefactObject']->getMailgroups()->toArray());

					return implode('; ', $arr);
				},
				'sortField'     => 'artefact.mailgroups',
			])

			->addColumn([
				'label'         => 'Délai(s) d\'alerte',
				'formatter'     => function ($row) {
					return null != $row['artefactObject']->getDelayExpired() ? $row['artefactObject']->getDelayExpired() . ' jour(s)' : '';
				},
				'formatter_csv' => function ($row) {
					return null != $row['artefactObject']->getDelayExpired() ? $row['artefactObject']->getDelayExpired() . ' jour(s)' : '';
				},
				'sortField'     => 'artefact.expired',
			])

			->addColumn([
				'label'         => 'Archivage',
				'formatter'     => function ($row) {
					return null === $row['artefactObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => function ($row) {
					return null === $row['artefactObject']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'sortField'     => 'artefact.deletedAt',
			])

			->addFilter([
				'label'       => 'Section',
				'field'       => 's.name',
				'selectLabel' => 's.name',
			], 'select')

			->addFilter([
				'label'       => 'Nom de l\'artefact',
				'field'       => 'artefact.name',
				'selectLabel' => 'artefact.name',
			], 'select')

			->addFilter([
				'label'       => 'N° de l\'artefact',
				'field'       => 'artefact.code',
				'selectLabel' => 'artefact.code',
			], 'select')

			->addFilter([
				'label'              => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field'              => 'artefact.deletedAt',
				'defaultValues'      => [0],
			], 'archived')

			->addAction([
				'href'      => function ($row) {
					return $this->router->generate('app.etmf.artefact.edit', ['artefactID' => $row['artefactObject']->getId()]);
				},
				'formatter' => function () {
					return '<i class="fa fa-edit"></i>';
				},
			])

			->addAction([
				'href'      => function ($row) {
					if (null === $row['artefactObject']->getDeletedAt()) {
						return $this->router->generate('app.etmf.artefact.archive', ['artefactID' => $row['artefactObject']->getId()]);
					} else {
						return $this->router->generate('app.etmf.artefact.restore', ['artefactID' => $row['artefactObject']->getId()]);
					}
				},
				'formatter' => function ($row) {
					if (null == $row['artefactObject']->getDeletedAt()) {
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
