<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\MembershipGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class MemberShipGroupFixtures
 * @package App\ESM\DataFixtures
 */
class MemberShipGroupFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$membershipGroups = [
			['label' => 'UCGI', 	'position' => 11,	'ref' => 'memberShipGroup-1'],
			['label' => 'ORL', 		'position' => 7, 	'ref' => 'memberShipGroup-2'],
			['label' => 'GMP', 		'position' => 6, 	'ref' => 'memberShipGroup-3'],
			['label' => 'SARCOME', 	'position' => 8, 	'ref' => 'memberShipGroup-4'],
			['label' => 'UCBG', 	'position' => 10, 	'ref' => 'memberShipGroup-5'],
			['label' => 'UNITRAD', 	'position' => 12, 	'ref' => 'memberShipGroup-6'],
			['label' => 'GIO', 		'position' => 5, 	'ref' => 'memberShipGroup-7'],
			['label' => 'GERICO', 	'position' => 3, 	'ref' => 'memberShipGroup-8'],
			['label' => 'SDS', 		'position' => 9, 	'ref' => 'memberShipGroup-9'],
			['label' => 'GETUG', 	'position' => 4, 	'ref' => 'memberShipGroup-10'],
			['label' => 'GEP', 		'position' => 2, 	'ref' => 'memberShipGroup-11'],
			['label' => 'FEDEGYN', 	'position' => 1, 	'ref' => 'memberShipGroup-12'],
			['label' => 'AUTRE', 	'position' => 13, 	'ref' => 'memberShipGroup-13'],
		];

		foreach ($membershipGroups as $membershipGroup) {
			$entity = new MembershipGroup();
			$entity->setLabel($membershipGroup['label']);
			$entity->setPosition($membershipGroup['position']);
			$this->addReference($membershipGroup['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['membershipGroup', 'project', 'prod'];
	}
}
