<?php

namespace App\ETMF\ListGen;

use App\ETMF\Entity\Mailgroup;
use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\ListGen;

/**
 * Class MailgroupList.
 */
class MailgroupList extends AbstractListGenType
{
	public function getList(): ListGen
	{
		$repository = $this->em->getRepository(Mailgroup::class);
		$list 		= $this->lg
			->setAjaxUrl($this->router->generate("app.etmf.mailgroup.list.ajax", []))
			->setClass('table')
			->setRepository($repository)
			->addHiddenData([
				'field' => 'mailgroup',
				'alias' => 'group',
			])
			->setRepositoryMethod('indexListGen')
		;

		$list
			->addColumn([
				'label' => 'entity.mailgroup.name',
				'formatter' => function ($row) {
					return $row['name'];
				},
				'formatter_csv' => function ($row) {
					return $row['name'];
				},
				'field' => 'mailgroup.name',
			])
			->addColumn([
				'label' => 'entity.mailgroup.users',
				'formatter' => function ($row) {
					$users = $row['group']->getUsers();
					$usersString = '';
					foreach ($users as $user) {
						$usersString .= $user->getEmail() . ' / ';
					}

					return $usersString;
				},
				'formatter_csv' => function ($row) {
					$users = $row['group']->getUsers();
					$usersString = '';
					foreach ($users as $user) {
						$usersString .= $user->getEmail() . ' / ';
					}

					return $usersString;
				},
				'field' => 'mailgroup.name',
			])

			->addAction([
				'href' => function ($row) {
					return $this->router->generate('app.etmf.mailgroup.edit', ['mailgroupID' => $row['group']->getId()]);
				},
				'formatter' => function () {
					return '<i class="fa fa-edit"></i>';
				},
			])

			->addAction([
				'href' => function ($row) {
					return $this->router->generate('app.etmf.mailgroup.delete', ['mailgroupID' => $row['group']->getId()]);
				},
				'formatter' => function () {
					return '<i class="fa fa-times c-red"></i>';
				},
			]);

		return $list;
	}
}
