<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Funding;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class FundingList.
 */
class FundingList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(string $locale, Project $project)
	{
		$repository = $this->em->getRepository(Funding::class);
		$url        = 'project.funding.index.ajax';
		$urlArgs    = ['id' => $project->getId()];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'f',
				'alias' => 'funding',
			])
			->addHiddenData([
				'field' => 'f.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGenProjectFunding', [$project->getId()])
			->setExportFileName('fundings')
			->addConstantSort('f.id', 'ASC');

		$list
			->addColumn([
				'label' => 'entity.Funding.field.funder',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return '<a href="' . $this->router->generate('project.fundings.show', ['id' => $project->getId(), 'idFunding' => $row['funding']->getId()]) . '">' . $row['funding']->getFunder() . '</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['funding']->getFunder();
				},
				'sortField' => 'f.funder',
			])
			->addColumn([
				'label' => 'entity.Funding.field.publicFundingShort',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return (1 == $row['funding']->getPublicFunding()) ? 'Oui' : 'Non';
				},
				'formatter_csv' => function ($row) use ($project) {
					return (1 == $row['funding']->getPublicFunding()) ? 'Oui' : 'Non';
				},
				'sortField' => 'f.funder',
			])
			->addColumn([
				'label' => 'entity.Funding.field.demandedAtShort',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return !is_null($row['funding']->getDemandedAt()) ? date_format($row['funding']->getDemandedAt(), 'd/m/Y') : ' ';
				},
				'formatter_csv' => function ($row) use ($project) {
					return !is_null($row['funding']->getDemandedAt()) ? date_format($row['funding']->getDemandedAt(), 'd/m/Y') : ' ';
				},
				'sortField' => 'f.callProfunderject',
			])
			->addColumn([
				'label' => 'entity.Funding.field.amountDevice',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return $row['funding']->getAmount() . $row['funding']->getDevise();
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['funding']->getAmount() . $row['funding']->getDevise();
				},
				'sortField' => 'f.publicFunding',
			])
			->addColumn([
				'label' => 'entity.Funding.field.obtainedAtShort',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return !is_null($row['funding']->getObtainedAt()) ? date_format($row['funding']->getObtainedAt(), 'd/m/Y') : ' ';
				},
				'formatter_csv' => function ($row) use ($project) {
					return !is_null($row['funding']->getObtainedAt()) ? date_format($row['funding']->getObtainedAt(), 'd/m/Y') : ' ';
				},
				'sortField' => 'f.callProfunderject',
			])
			->addColumn([
				'label' => 'entity.Funding.field.comment',
				'formatter' => function ($row) use ($project) {
					return $row['funding']->getComment() ?? '';
				},
				'formatter_csv' => function ($row) use ($project) {
					return $row['funding']->getComment() ?? '';
				},
				'sortField' => 'f.comment',
			])
			->addAction([
				'href' => function ($row) use ($project) {
					return $this->router->generate('project.fundings.show', ['id' => $project->getId(), 'idFunding' => $row['funding']->getId()]);
				},
				'formatter' => function ($row) {
					return '<i class="fa fa-eye c-grey"></i>';
				},
			])
			->addFilter([
				'label' => 'entity.Project.publication.register.labels.name',
				'field' => 'fu.id',
				'selectLabel' => 'fu.label',
			], 'select');

		if ($this->security->isGranted('ROLE_FUNDING_ARCHIVE')) {
			$list
				->addColumn([
					'label' => 'entity.status.label',
					'formatter' => function ($row) {
						return null === $row['funding']->getDeletedAt() ? 'Non archivé' : 'Archivé';
					},
					'formatter_csv' => function ($row) {
						return null === $row['funding']->getDeletedAt() ? 'Non archivé' : 'Archivé';
					},
					'sortField' => 'f.deletedAt',
				])
				->addFilter([
					'label' => 'filter.archived.label',
					'translation_domain' => 'ListGen',
					'field' => 'f.deletedAt',
					'defaultValues' => [0],
				], 'archived');
		}

		return $list;
	}
}
