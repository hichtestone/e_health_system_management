<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Center;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;

/**
 * Class CenterFixtures
 * @package App\ESM\DataFixtures
 */
class CenterFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
	    $projectRefs = ['project-1', 'project-2'];
	    $centerStatusRefs = ['centreStatus-1', 'centreStatus-2', 'centreStatus-3', 'centreStatus-7', 'centreStatus-11', 'centreStatus-12'];
        $institutionRefsProject1 = ['institution-1', 'institution-3', 'institution-5'];
        $institutionRefsProject2 = [ 'institution-1', 'institution-3', 'institution-4',];

        foreach ($projectRefs as $projectRef) {
            for ($i = 1 ; $i < 4; $i++) {
                $center = new Center();
                $center->setNumber('00' . $i);
                $center->setName('Centre ' . $i);
                $center->setProject($this->getReference($projectRef));

                if ($projectRef === 'project-1') {
                    foreach ($institutionRefsProject1 as $valueProject1) {
                        $center->addInstitution($this->getReference($valueProject1));
                    }
                } else {
                    foreach ($institutionRefsProject2 as $valueProject2) {
                        $center->addInstitution($this->getReference($valueProject2));
                    }
                }
                $key = array_rand($centerStatusRefs);
                $center->setCenterStatus($this->getReference($centerStatusRefs[$key]));

                $this->addReference('center-'. $i . '-'. $projectRef, $center);
                $manager->persist($center);
            }
        }

        $manager->flush();
	}

	public static function getGroups(): array
	{
		return ['center'];
	}

	public function getDependencies(): array
	{
		return [
			InstitutionFixtures::class,
			ProjectFixtures::class,
			CenterStatusFixtures::class
		];
	}
}
