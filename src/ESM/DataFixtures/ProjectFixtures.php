<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Date;
use App\ESM\Entity\Project;
use App\ESM\Entity\Rule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ProjectFixtures
 * @package App\DataFixtures
 */
class ProjectFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$project1 = new Project();
		$project1->setName('the best of project');
		$project1->setAppToken('60fe88aec2081');
		$project1->setUpdatedAt(new \DateTime());
		$project1->setAcronyme('BOP');
		$project1->setNameEnglish('the best of project');
		$project1->setCrfType($this->getReference('crfType-1'));
		$project1->setTrailType($this->getReference('trailType-1'));
		$project1->setTrailPhase($this->getReference('trailPhase-1'));
		$project1->setSponsor($this->getReference('sponsor-1'));
		$project1->setResponsiblePM($this->getReference('user-cdp'));
		$project1->setResponsibleCRA($this->getReference('user-admin'));
		$project1->setProjectStatus($this->getReference('projectStatus-1'));
		$project1->setTerritory($this->getReference('territory-1'));

		$this->addReference('project-1', $project1);

		$manager->persist($project1);

        // Create new Date entity
        $date = new Date();
        $date->setProject($project1);
        $manager->persist($date);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($project1);
        $manager->persist($rule);

		$manager->flush();

		$project2 = new Project();
		$project2->setName('project test for developing');
		$project2->setAppToken('60fe88a81');
		$project2->setUpdatedAt(new \DateTime());
		$project2->setAcronyme('TFD');
		$project2->setNameEnglish('project test for developing');
		$project2->setCrfType($this->getReference('crfType-2'));
		$project2->setTrailType($this->getReference('trailType-2'));
		$project2->setTrailPhase($this->getReference('trailPhase-2'));
		$project2->setSponsor($this->getReference('sponsor-2'));
		$project2->setResponsiblePM($this->getReference('user-cdp'));
		$project2->setResponsibleCRA($this->getReference('user-admin'));
		$project2->setTerritory($this->getReference('territory-2'));
		$project2->addCountry($this->getReference('country-2'));
		$project2->addCountry($this->getReference('country-3'));
		$project2->addCountry($this->getReference('country-5'));
		$project2->setStudyPopulation([0]);

		$this->addReference('project-2', $project2);

		$manager->persist($project2);

        // Create new Date entity
        $date = new Date();
        $date->setProject($project2);
        $manager->persist($date);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($project2);
        $manager->persist($rule);

		$manager->flush();

		$project3 = new Project();
		$project3->setName('project of the year');
		$project3->setAppToken('60fe4T668a81');
		$project3->setUpdatedAt(new \DateTime());
		$project3->setAcronyme('POY');
		$project3->setNameEnglish('project of the year');
		$project3->setCrfType($this->getReference('crfType-4'));
		$project3->setTrailType($this->getReference('trailType-1'));
		$project3->setTrailPhase($this->getReference('trailPhase-4'));
		$project3->setSponsor($this->getReference('sponsor-4'));
		$project3->setResponsiblePM($this->getReference('user-cdp'));
		$project3->setResponsibleCRA($this->getReference('user-admin'));
		$project3->setTerritory($this->getReference('territory-1'));
		$project3->addCountry($this->getReference('country-4'));
		$project3->addCountry($this->getReference('country-5'));
		$project3->addCountry($this->getReference('country-6'));
		$project3->setStudyPopulation([0]);

		$this->addReference('project-3', $project3);

		$manager->persist($project3);

        // Create new Date entity
        $date = new Date();
        $date->setProject($project3);
        $manager->persist($date);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($project3);
        $manager->persist($rule);

		$manager->flush();

		$project4 = new Project();
		$project4->setName('project of the koh-lanta');
		$project4->setAppToken('60fe884567a81');
		$project4->setUpdatedAt(new \DateTime());
		$project4->setAcronyme('PKL');
		$project4->setNameEnglish('project of the koh-lanta');
		$project4->setCrfType($this->getReference('crfType-3'));
		$project4->setTrailType($this->getReference('trailType-3'));
		$project4->setTrailPhase($this->getReference('trailPhase-5'));
		$project4->setSponsor($this->getReference('sponsor-7'));
		$project4->setResponsiblePM($this->getReference('user-cdp'));
		$project4->setResponsibleCRA($this->getReference('user-admin'));
		$project4->setTerritory($this->getReference('territory-2'));
		$project4->addCountry($this->getReference('country-7'));
		$project4->addCountry($this->getReference('country-8'));
		$project4->addCountry($this->getReference('country-9'));
		$project4->setStudyPopulation([0]);

		$this->addReference('project-4', $project4);

		$manager->persist($project4);

        // Create new Date entity
        $date = new Date();
        $date->setProject($project4);
        $manager->persist($date);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($project4);
        $manager->persist($rule);

		$manager->flush();

		$project5 = new Project();
		$project5->setName('project of the death megadrums');
		$project5->setAppToken('60fe88333333456a81');
		$project5->setUpdatedAt(new \DateTime());
		$project5->setAcronyme('PDMD');
		$project5->setNameEnglish('project of the death megadrums');
		$project5->setCrfType($this->getReference('crfType-1'));
		$project5->setTrailType($this->getReference('trailType-5'));
		$project5->setTrailPhase($this->getReference('trailPhase-7'));
		$project5->setSponsor($this->getReference('sponsor-10'));
		$project5->setResponsiblePM($this->getReference('user-cdp'));
		$project5->setResponsibleCRA($this->getReference('user-admin'));
		$project5->setTerritory($this->getReference('territory-1'));
		$project5->addCountry($this->getReference('country-2'));
		$project5->addCountry($this->getReference('country-7'));
		$project5->addCountry($this->getReference('country-10'));
		$project5->setStudyPopulation([0]);

		$this->addReference('project-5', $project5);

		$manager->persist($project5);

        // Create new Date entity
        $date = new Date();
        $date->setProject($project5);
        $manager->persist($date);

        // Create new Rule entity
        $rule = new Rule();
        $rule->setProject($project5);
        $manager->persist($rule);


		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['project', 'center'];
	}

	public function getDependencies(): array
	{
		return [
			CrfTypeFixtures::class,
			TrailTypeFixtures::class,
			TrailPhaseFixtures::class,
			MemberShipGroupFixtures::class,
			SponsorFixtures::class,
			UserFixtures::class,
			ProjectStatusFixtures::class,
			TerritoryFixtures::class
		];
	}
}
