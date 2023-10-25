<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\Project;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class InterlocutorCenterList.
 */
class InterlocutorCenterList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(string $locale, Project $project)
	{
		$repository = $this->em->getRepository(Interlocutor::class);
		$url        = 'project.interlocutors.index.ajax';
		$urlArgs    = ['id' => $project->getId()];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 'int',
				'alias' => 'interlocutor',
			])
			->addHiddenData([
				'field' => 'int.id',
				'alias' => 'id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGenByProject', [$project])
			->setExportFileName('interlocutorsProject')
			->addConstantSort('int.id', 'ASC')
			->addMultiAction([
			    //entity.Contact.action.createn => TODO trad action listgen
				'label' => 'Créer un contact',
				'href' => $this->router->generate('project.contact.new', ['id' => $project->getId(), 'from' => 'interlocutor']),
				'displayer' => function ($security) use ($project) {
					return $security->isGranted('PROJECT_ACCESS_AND_OPEN', $project) && $security->isGranted('ROLE_COMMUNICATION_WRITE');
				},
				'data' => ['redirect' => 'interlocutors_ids'],
			]);

		$list
			->addColumn([
				'label' => 'entity.Interlocutor.field.interlocutor',
				'formatter' => function ($row) use ($project) {
					return '<a href="' . $this->router->generate('project.interlocutor.show',
							['id' => $project->getId(), 'idInterlocutor' => $row['interlocutor']->getId()]) . '">'
						. $row['interlocutor']->getFullName() . '</a>';
				},
				'formatter_csv' => function ($row) {
					return $row['interlocutor']->getFullName();
				},
				'field' => 'int.lastName',
			])
			->addColumn([
				'label' => 'entity.Center.label',
				'formatter' => function ($row) use ($project) {
					$arr = array_map(function ($interlocutorCenter) use ($project) {
						if ($project->getId() == $interlocutorCenter->getCenter()->getProject()->getId()) {
							return '<a href="' . $this->router->generate('project.center.show', ['id' => $project->getId(), 'idCenter' => $interlocutorCenter->getCenter()->getId()]) . '">' . $interlocutorCenter->getCenter()->getNumber() . ' ' . $interlocutorCenter->getCenter()->getName() . '</a>';
						}
					}, $row['interlocutor']->getInterlocutorCenters()->toArray());

					return implode('<br />', array_filter($arr));
				},
				'formatter_csv' => function ($row) {
					$arr = array_map(function ($center) {
						return $center->getCenter()->getNumber() . ' ' . $center->getCenter()->getName();
					}, $row['interlocutor']->getInterlocutorCenters()->toArray());

					return implode('; ', $arr);
				},
				'field' => 'c.number',
			])
			->addColumn([
				'label' => 'entity.Interlocutor.field.job',
				'field' => 'j.label',
				'formatter' => function ($row) {
					return $row['interlocutor']->getJob()->getLabel();
				},
				'formatter_csv' => function ($row) {
					return $row['interlocutor']->getJob()->getLabel();
				},
			])
			->addColumn([
				'label' => 'entity.Interlocutor.field.email',
				'field' => 'int.email',
				'formatter' => function ($row) {
					return $row['interlocutor']->getEmail();
				},
				'formatter_csv' => function ($row) {
					return $row['interlocutor']->getEmail();
				},
			])
			->addColumn([
				'label' => 'entity.Interlocutor.field.phone',
				'field' => 'int.phone',
				'formatter' => function ($row) {
					return (string) $row['interlocutor']->getPhone();
				},
				'formatter_csv' => function ($row) {
					return (string) $row['interlocutor']->getPhone();
				},
			])
			->addFilter([
				'label' => 'form.field.name',
				'field' => 'CONCAT(int.firstName,int.lastName)',
			], 'string')
			->addFilter([
				'label' => 'entity.Center.label',
				'field' => 'c.id',
				'selectLabel' => 'c.number',
			], 'select');

		return $list;
	}
}
