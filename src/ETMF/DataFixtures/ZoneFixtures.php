<?php

namespace App\ETMF\DataFixtures;

use App\ETMF\Entity\Zone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class ZoneFixtures
 * @package App\ETMF\DataFixtures
 */
class ZoneFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
        for($i = 1; $i < 16; $i++) {

            $faker = Factory::create('fr_FR');
            $zone = new Zone();
            $zone->setName('Zone ' . $faker->unique->text($faker->numberBetween(5, 10)) . $i);
            $zone->setCode($faker->randomDigitNotZero());
            $this->setReference('zone-'.$i, $zone);
            $manager->persist($zone);
        }

        $manager->flush();
	}

	public static function getGroups(): array
	{
		return ['zone', 'section', 'mailgroup', 'artefact', 'documentVersion'];
	}
}
