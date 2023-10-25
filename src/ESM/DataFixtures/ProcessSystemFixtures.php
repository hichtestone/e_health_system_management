<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\DeviationSystem\ProcessSystem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ProcessSystemFixtures
 * @package App\ESM\DataFixtures
 */
class ProcessSystemFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$processSystems = [
			['code' => 'M1', 'label' => 'Mettre en oeuvre la stratégie et le développement des activités de recherche', 'ref' => 'processSystem1'],
			['code' => 'M2', 'label' => 'promouvoir l\'amélioration continue', 'ref' => 'processSystem2'],
			['code' => 'M3', 'label' => 'développer le capital humain', 'ref' => 'processSystem3'],
			['code' => 'R1', 'label' => 'Constituer et maintenir une plateforme de données de vraie vie', 'ref' => 'processSystem5'],
			['code' => 'R2', 'label' => 'Exploiter et valoriser une plateforme de données de vraie vie', 'ref' => 'processSystem6'],
			['code' => 'R3', 'label' => 'Concevoir, valider et mettre en oeuvre le projet dessai clinique', 'ref' => 'processSystem7'],
			['code' => 'R4', 'label' => 'Conduire et clôturer l\'EC', 'ref' => 'processSystem8'],
			['code' => 'R5', 'label' => 'Surveiller et gérer la sécurité des EC', 'ref' => 'processSystem9'],
			['code' => 'S1', 'label' => 'Promouvoir les activités de la R&D Unicancer', 'ref' => 'processSystem10'],
			['code' => 'S2', 'label' => 'Adapter les outils SI', 'ref' => 'processSystem11'],
			['code' => 'S3', 'label' => 'Piloter le suivi financier de la R&D', 'ref' => 'processSystem12'],
		];

		foreach ($processSystems as $processSystem) {
			$entity = new ProcessSystem();
			$entity->setCode($processSystem['code']);
			$entity->setLabel($processSystem['label']);
			$this->setReference($processSystem['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['processSystem', 'prod'];
	}
}
