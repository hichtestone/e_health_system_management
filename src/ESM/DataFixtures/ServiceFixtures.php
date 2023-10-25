<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Service;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class SettingsFixtures
 * @package App\ESM\DataFixtures
 */
class ServiceFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
	    $slugify = new Slugify();
	    $cities = ['Bordeaux', 'Lyon', 'Paris', 'Marseille', 'Lille'];

	    for ($i = 1; $i <count($cities); $i++) {
            $service = new Service();
            $service->setName('service chirurgie ' . $cities[$i-1]);
            $service->setCity($cities[$i-1]);
            $service->setAddressInherited(true);
            $service->setInstitution($this->getReference('institution-' . $i));
            $service->setSlug($slugify->slugify('service chirurgie ' . $cities[$i-1]));

            $this->setReference('service-' . $i, $service);

            $manager->persist($service);
        }

		$manager->flush();

	}

	public static function getGroups(): array
	{
		return ['service', 'institution'];
	}

	public function getDependencies(): array
	{
		return [
			InstitutionFixtures::class
		];
	}
}
