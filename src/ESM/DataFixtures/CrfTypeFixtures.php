<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\CrfType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CrfTypeFixtures
 * @package App\ESM\DataFixtures
 */
class CrfTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		// crf type
		$crtTypes = [
			['label' => 'Clinfile', 'code' => 'clinfile', 	'ref' => 'crfType-1'],
			['label' => 'Papier', 	'code' => 'papier', 	'ref' => 'crfType-2'],
			['label' => 'Ennov', 	'code' => 'ennov', 		'ref' => 'crfType-3'],
			['label' => 'Autre', 	'code' => 'autre', 		'ref' => 'crfType-4'],
		];

		foreach ($crtTypes as $type) {
			$entity = new CrfType();
			$entity->setLabel($type['label']);
			$entity->setCode($type['code']);
			$this->addReference($type['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['crfType', 'project', 'prod'];
	}
}
