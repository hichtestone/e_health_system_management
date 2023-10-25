<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ContactObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ContactObjectFixtures
 * @package App\ESM\DataFixtures
 */
class ContactObjectFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{

		// exam type
		$contactObjects = [
			['label' => 'participation', 'code' => 'participation', 'ref' => 'contactObject-1'],
			['label' => 'recrutement', 'code' => 'recruitment', 'ref' => 'contactObject-2'],
			['label' => 'logistique', 'code' => 'logistics', 'ref' => 'contactObject-3'],
			['label' => 'suivi monitoring', 'code' => 'monitoring', 'ref' => 'contactObject-4'],
			['label' => 'déviation protocolaire', 'code' => 'deviation-protocol', 'ref' => 'contactObject-5'],
			['label' => 'autre', 'code' => 'other', 'ref' => 'contactObject-6'],
		];

		foreach ($contactObjects as $contactObject) {

			$entity = new ContactObject();
			$entity->setLabel($contactObject['label']);
			$entity->setCode($contactObject['code']);
			$this->setReference($contactObject['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userProject', 'user', 'project', 'prod'];
	}
}
