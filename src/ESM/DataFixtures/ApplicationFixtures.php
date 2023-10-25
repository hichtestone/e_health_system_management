<?php

declare(strict_types=1);

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ApplicationFixtures.
 */
class ApplicationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $app = new Application();
        $app->setName('ESM');
        $app->setType($this->getReference(ApplicationTypeFixtures::APPTYPE_ESM));
        $app->setImg('/img/app/portal.png');
        $app->setUrl('http://esm-template-v3.localhost');
        $app->setApiToken(md5(random_bytes(64)));

        $manager->persist($app);
        $manager->flush();
    }

	public static function getGroups(): array
	{
		return ['application', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			ApplicationTypeFixtures::class,
		];
	}
}
