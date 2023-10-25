<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Society;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

/**
 * Class UserFixtures
 * @package App\ESM\DataFixtures
 */
class SocietyFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$societyCounter = 1;
		foreach (['SociétéA', 'SociétéB', 'unicancer'] as $item) {

			$entity = new Society();
			$entity->setName($item);

			$this->setReference('society-'.$societyCounter, $entity);

			$manager->persist($entity);
			$manager->flush();

			$societyCounter++;
		}
	}

	public static function getGroups(): array
	{
		return ['user', 'userCreate', 'recette-light', 'prod'];
	}
}
