<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\TrailPhase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TrailPhaseFixtures
 * @package App\ESM\DataFixtures
 */
class TrailPhaseFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$trailPhases = [
			['label' => 'I', 		'ref' => 'trailPhase-1'],
			['label' => 'II', 		'ref' => 'trailPhase-2'],
			['label' => 'III', 		'ref' => 'trailPhase-3'],
			['label' => 'IV', 		'ref' => 'trailPhase-4'],
			['label' => 'Cohorte', 	'ref' => 'trailPhase-5'],
			['label' => 'HPS', 		'ref' => 'trailPhase-6'],
			['label' => 'NA', 		'ref' => 'trailPhase-7'],
		];

		foreach ($trailPhases as $trailPhase) {
			$entity = new TrailPhase();
			$entity->setLabel($trailPhase['label']);
			$this->addReference($trailPhase['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['trailPhase', 'project', 'prod'];
	}
}
