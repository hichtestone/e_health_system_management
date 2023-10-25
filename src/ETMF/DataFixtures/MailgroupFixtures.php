<?php

namespace App\ETMF\DataFixtures;

use App\ESM\DataFixtures\UserFixtures;
use App\ETMF\Entity\Mailgroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class MailgroupFixtures
 * @package App\ETMF\DataFixtures
 */
class MailgroupFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
		$nbMailgroup = 15;

        for ($refMailgroup = 1; $refMailgroup <= $nbMailgroup; $refMailgroup++) {

            $faker = Factory::create('fr_FR');
            $mailGroup = new Mailgroup();
            $mailGroup->setName('Groupe ' . $faker->unique->text($faker->numberBetween(5, 10)) . $refMailgroup);

			for ($j = 1; $j < $faker->numberBetween(1, 4); $j++) {

                $mailGroup->addUser($this->getReference('user-admin'));
                $mailGroup->addUser($this->getReference('user-' . rand(1, 10)));
            }

            $this->setReference('mailgroup-' . $refMailgroup, $mailGroup);
            $manager->persist($mailGroup);
			$manager->flush();
		}
    }

	public function getDependencies(): array
	{
		return [
			UserFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['zone', 'section', 'mailgroup', 'artefact', 'documentVersion'];
	}
}
