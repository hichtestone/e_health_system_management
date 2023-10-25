<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\PatientVariable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PatientDataFixtures
 * @package App\ESM\DataFixtures
 */
class PatientDataFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
        $projectRefs = ['project-1', 'project-2'];
        $variables = ['Date de signature du consentement', 'Date d\'inclusion'];

        foreach ($projectRefs as $projectRef) {
            foreach ($variables as $variable) {
                $patientVariable = new PatientVariable();
                $patientVariable->setProject($this->getReference($projectRef));
                $patientVariable->setVariableType($this->getReference('variableType-2'));
                $patientVariable->setLabel($variable);
                $patientVariable->setIsVariable(true);
                $patientVariable->setHasPatient(true);
                $patientVariable->setSys(true);

                $manager->persist($patientVariable);
            }
        }

		$manager->flush();


	}

	public static function getGroups(): array
	{
		return ['patientData'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			VariableTypeFixtures::class
		];
	}
}
