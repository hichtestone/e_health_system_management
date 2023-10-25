<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Territory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TrailPhaseFixtures
 * @package App\ESM\DataFixtures
 */
class TerritoryFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$territory1 = new Territory();
		$territory1->setLabel('France');
		$territory1->setDeleteAt(null);
		$this->setReference('territory-1', $territory1);

		$manager->persist($territory1);
		$manager->flush();

		$territory2 = new Territory();
		$territory2->setLabel('International');
		$territory2->setDeleteAt(null);
		$this->setReference('territory-2', $territory2);

		$manager->persist($territory2);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['territory', 'project', 'prod'];
	}
}
