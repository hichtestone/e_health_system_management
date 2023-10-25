<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Civility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CivilityFixtures
 * @package App\ESM\DataFixtures
 */
class CivilityFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$civilities = [
			['label' => 'Ms.', 'code' => 'MS', 'ref' => 'civility-1'],
			['label' => 'Mr.', 'code' => 'MR', 'ref' => 'civility-2'],
			['label' => 'Dr.', 'code' => 'DR', 'ref' => 'civility-3'],
			['label' => 'Pr.', 'code' => 'PR', 'ref' => 'civility-4']
		];

		foreach ($civilities as $civility) {

			$entity = new Civility();
			$entity->setLabel($civility['label']);
			$entity->setCode($civility['code']);
			$this->setReference($civility['ref'], $entity);
			$manager->persist($entity);
			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['civility', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			CenterFixtures::class
		];
	}
}
