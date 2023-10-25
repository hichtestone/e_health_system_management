<?php

declare(strict_types=1);

namespace App\ESM\DataFixtures;

use App\ESM\Entity\TermsOfService;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TermsOfServiceFixtures
 * @package App\ESM\DataFixtures
 */
class TermsOfServiceFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $tos = new TermsOfService();
        $tos->setPublishedAt(new DateTime());

        $manager->persist($tos);
        $manager->flush();
    }

	public static function getGroups(): array
	{
		return ['prod'];
	}
}
