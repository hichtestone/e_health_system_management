<?php

namespace App\ETMF\DataFixtures;

use App\ETMF\Entity\DropdownList\DocumentLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class DocumentLevelFixtures
 * @package App\ETMF\DataFixtures
 */
class DocumentLevelFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$documentLevels = [
			['level' => 'pays',  'code' => 'country', 'ref' => 'country'],
			['level' => 'centre', 'code' => 'center', 'ref' => 'center'],
		];

		foreach ($documentLevels as $documentLevel) {

			$entity = new DocumentLevel();
			$entity->setLevel($documentLevel['level']);
			$entity->setCode($documentLevel['code']);
			$this->setReference($documentLevel['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

    /**
     * @return string[]
     */
	public static function getGroups(): array
	{
		return ['prod'];
	}
}
