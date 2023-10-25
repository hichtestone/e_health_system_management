<?php

namespace App\ETMF\DataFixtures;

use App\ESM\DataFixtures\UserFixtures;
use App\ETMF\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class TagFixtures
 * @package App\ETMF\DataFixtures
 */
class TagFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
        for($i = 1; $i < 11; $i++) {

            $faker = Factory::create('fr_FR');
            $tag = new Tag();
            $tag->setName('Tag ' . $faker->unique->text($faker->numberBetween(5, 10)) . $i);
            $tag->setCreatedBy($this->getReference('user-admin'));

			$this->setReference('tag-'.$i, $tag);
            $manager->persist($tag);
			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['zone', 'section', 'mailgroup', 'artefact', 'documentVersion'];
	}

	public function getDependencies(): array
	{
		return [
			UserFixtures::class,
		];
	}
}
