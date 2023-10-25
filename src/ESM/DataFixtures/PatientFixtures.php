<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PatientFixtures
 * @package App\ESM\DataFixtures
 */
class PatientFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		/*$patient1 = new Patient();
		$patient1->setNumber('FRED321');
		$patient1->setProject($this->getReference('project-1'));
		$patient1->setCenter($this->getReference('center-1'));

		$this->setReference('patient-1', $patient1);

		$manager->persist($patient1);
		$manager->flush();

		$patient2 = new Patient();
		$patient2->setNumber('V49GJ5');
		$patient2->setProject($this->getReference('project-1'));
		$patient2->setCenter($this->getReference('center-1'));

		$this->setReference('patient-2', $patient2);

		$manager->persist($patient2);
		$manager->flush();

		$patient3 = new Patient();
		$patient3->setNumber('FVKTL894');
		$patient3->setProject($this->getReference('project-1'));
		$patient3->setCenter($this->getReference('center-2'));

		$this->setReference('patient-3', $patient3);

		$manager->persist($patient3);
		$manager->flush();

		$patient4 = new Patient();
		$patient4->setNumber('FREVFFK5321');
		$patient4->setProject($this->getReference('project-1'));
		$patient4->setCenter($this->getReference('center-1'));

		$this->setReference('patient-4', $patient4);

		$manager->persist($patient4);
		$manager->flush();*/
	}

	public static function getGroups(): array
	{
		return ['patient'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			CenterFixtures::class
		];
	}
}
