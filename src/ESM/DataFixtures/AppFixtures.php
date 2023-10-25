<?php

namespace App\ESM\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 * @package App\ESM\DataFixtures
 */
class AppFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const FIRST_USER = 'FIRST_USER';
    public const PROFILE_ADMIN = 'PROFILE_ADMIN';
    public const PROFILE_USER = 'PROFILE_USER';
    public const APPTYPE_ESM = 'APPTYPE_ESM';

    public function getDependencies(): array
    {
        return [
            DropDownListFixtures::class,
            RoleFixtures::class,
            ProfileFixtures::class,
            TermsOfServiceFixtures::class,
            ApplicationTypeFixtures::class,
            ApplicationFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
    }

	public static function getGroups(): array
	{
		return ['prod'];
	}
}
