<?php

namespace App\ETMF\DataFixtures;

use App\ETMF\Entity\DropdownList\ArtefactLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ArtefactLevelFixtures
 * @package App\ETMF\DataFixtures
 */
class ArtefactLevelFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$artefactLevels = [
			['level' => 'pays',  'code' => 'country', 'ref' => 'artefact-country'],
			['level' => 'centre', 'code' => 'center', 'ref' => 'artefact-center'],
		];

		foreach ($artefactLevels as $artefactLevel) {
			$entity = new ArtefactLevel();
			$entity->setLevel($artefactLevel['level']);
			$entity->setCode($artefactLevel['code']);
			$this->setReference($artefactLevel['ref'], $entity);
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
