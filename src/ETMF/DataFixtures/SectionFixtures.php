<?php

namespace App\ETMF\DataFixtures;

use App\ETMF\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class SectionFixtures
 * @package App\ETMF\DataFixtures
 */
class SectionFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
	/**
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager): void
	{
		$nbZones 		 = 15;
		$nbSectionByZone = 5;

		for ($refZone = 1 ; $refZone <= $nbZones ; $refZone++) {

			for ($refSection = 1 ; $refSection <= $nbSectionByZone ; $refSection++) {

				$faker = Factory::create('fr_FR');

				$section = new Section();
				$section->setName('Section ' . $faker->unique->text($faker->numberBetween(5, 10)) . $refSection);
				$section->setCode($faker->randomDigitNotZero());
				$section->setZone($this->getReference('zone-' . $refZone));
				$this->setReference('section-' . $refZone . '-' . $refSection, $section);

				$manager->persist($section);
				$manager->flush();
			}
		}
	}

	public function getDependencies(): array
	{
		return [
			ZoneFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['zone', 'section', 'mailgroup', 'artefact', 'documentVersion'];
	}
}
