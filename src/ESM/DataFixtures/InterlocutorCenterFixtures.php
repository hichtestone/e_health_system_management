<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\InterlocutorCenter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class InterlocutorCenterFixtures
 * @package App\ESM\DataFixtures
 */
class InterlocutorCenterFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
        $projectRefs = ['project-1', 'project-2'];
        $interlocutorRefs = ['interlocutor-1', 'interlocutor-2'];


        foreach ($projectRefs as $projectRef) {

            $i = 1;
            foreach ($interlocutorRefs as $interlocutorRef) {

                $interlocutorCenter = new InterlocutorCenter();

                $random = rand(1, 3);

                $interlocutorCenter->setInterlocutor($this->getReference($interlocutorRef));
                $interlocutorCenter->setCenter($this->getReference('center-'. $random . '-' . $projectRef));
                $interlocutorCenter->setService($this->getReference('service-1'));

                if ($interlocutorRef === 'interlocutor-1') {
                    $interlocutorCenter->setIsPrincipalInvestigator(true);
                }

                $this->addReference('interlocutorCentre-'. $i. '-' . $projectRef, $interlocutorCenter);
                $manager->persist($interlocutorCenter);

                $i++;
            }
        }
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['interlocutorCenter'];
	}

	public function getDependencies(): array
	{
		return [
			InterlocutorFixtures::class,
			CenterFixtures::class,
			ServiceFixtures::class
		];
	}
}
