<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Visit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class VisitFixtures
 * @package App\DataFixtures
 */
class VisitFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$visit1 = new Visit();
		$visit1->setOrdre(10);
		$visit1->setLabel('visit de test');
		$visit1->setProject($this->getReference('project-1'));
		$visit1->setShort('short');
		$visit1->setPrice(100);
		$visit1->setPhase($this->getReference('phase-setting-1'));
		$visit1->setDelay(5);
		$visit1->setPosition(3);
		$visit1->setPatientVariable($this->getReference('patient-variable-1'));
		$visit1->addVariable($this->getReference('patient-variable-1'));

		$this->setReference('visit-1', $visit1);

		$manager->persist($visit1);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['visit'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			PhaseSettingFixtures::class,
			PatientVariableFixtures::class
		];
	}
}
