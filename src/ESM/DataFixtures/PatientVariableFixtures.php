<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\PatientVariable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PatientDataFixtures
 * @package App\DataFixtures
 */
class PatientVariableFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$patientVariable1 = new PatientVariable();
		$patientVariable1->setProject($this->getReference('project-1'));
		$patientVariable1->setVariableType($this->getReference('variableType-2'));
		$patientVariable1->setLabel('Date de signature du consentement');
		$patientVariable1->setIsVariable(true);
		$patientVariable1->setHasPatient(true);
		$patientVariable1->setSys(true);

		$this->setReference('patient-variable-1', $patientVariable1);

		$manager->persist($patientVariable1);
		$manager->flush();

		$patientVariable2 = new PatientVariable();
		$patientVariable2->setProject($this->getReference('project-1'));
		$patientVariable2->setVariableType($this->getReference('variableType-2'));
		$patientVariable2->setLabel('Date d\'inclusion');
		$patientVariable2->setIsVariable(true);
		$patientVariable2->setHasPatient(true);
		$patientVariable2->setSys(true);

		$this->setReference('patient-variable-2', $patientVariable2);

		$manager->persist($patientVariable2);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['patientVariable'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			VariableTypeFixtures::class
		];
	}
}
