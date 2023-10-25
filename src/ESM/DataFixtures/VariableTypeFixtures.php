<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\VariableType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class VariableTypeFixtures
 * @package App\ESM\DataFixtures
 */
class VariableTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$variableTypes = [
			['numeric', 'variableType-1'],
			['date',    'variableType-2'],
			['string',  'variableType-3'],
			['list',    'variableType-4'],
		];

		foreach ($variableTypes as $item) {

			$entity = new VariableType();
			$entity->setLabel($item[0]);
			$this->setReference($item[1], $entity);

			$manager->persist($entity);
			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['variableType', 'prod'];
	}
}
