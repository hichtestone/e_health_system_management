<?php

declare(strict_types=1);

namespace App\ESM\DataFixtures;

use App\ESM\Entity\ApplicationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ApplicationTypeFixtures.
 */
class ApplicationTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public const APPTYPE_ESM = 'APPTYPE_ESM';

	public function load(ObjectManager $manager): void
    {
        // portal
        $appType = new ApplicationType();
        $appType->setName('Portal v2.0');
        $manager->persist($appType);

        // eTMF
        $appType = new ApplicationType();
        $appType->setName('eTMF v1.0');
        $manager->persist($appType);

        // ICC
        $appType = new ApplicationType();
        $appType->setName('ICC v1.0');
        $manager->persist($appType);
        $manager->flush();

        // eCRF
        $appType = new ApplicationType();
        $appType->setName('ERDC v6.1');
        $manager->persist($appType);
        $manager->flush();

        // CTMS
        $appType = new ApplicationType();
        $appType->setName('ESM v3.0');
        $manager->persist($appType);
        $manager->flush();
        $this->addReference(self::APPTYPE_ESM, $appType);

        // EDMS
        $appType = new ApplicationType();
        $appType->setName('EDMS v2.1');
        $manager->persist($appType);
        $manager->flush();
    }

	public static function getGroups(): array
	{
		return ['prod'];
	}
}
