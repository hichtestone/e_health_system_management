<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ProjectStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ProjectFixtures
 * @package App\ESM\DataFixtures
 */
class ProjectStatusFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$projectStatusAll = [
			['label' => 'En discussion',	'code' => 'discussion',  	'ref' => 'projectStatus-1'],
			['label' => 'En initiation',	'code' => 'initiation',   	'ref' => 'projectStatus-2'],
			['label' => 'En recrutement',	'code' => 'recrutement',   	'ref' => 'projectStatus-3'],
			['label' => 'En suivi',			'code' => 'suivi',   		'ref' => 'projectStatus-4'],
			['label' => 'A clôturer',		'code' => 'a-cloturer',   	'ref' => 'projectStatus-5'],
			['label' => 'Clôturé',			'code' => 'clos',   		'ref' => 'projectStatus-6'],
		];

		foreach ($projectStatusAll as $projectStatus) {

			$entity = new ProjectStatus();
			$entity->setLabel($projectStatus['label']);
			$entity->setCode($projectStatus['code']);
			$this->addReference($projectStatus['ref'], $entity);
			$manager->persist($entity);
			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['project', 'prod'];
	}
}
