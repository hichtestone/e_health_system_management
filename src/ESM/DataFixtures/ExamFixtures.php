<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Exam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ExamFixtures
 * @package App\DataFixtures
 */
class ExamFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$exam1 = new Exam();
		$exam1->setPosition(10);
		$exam1->setPrice(50);
		$exam1->setProject($this->getReference('project-1'));
		$exam1->setOrdre(10);
		$exam1->setType($this->getReference('exam-2'));
		$exam1->setName('test exam');
		$exam1->setTypeReason('ceci est une bonne raison');
		$exam1->addVariable($this->getReference('patient-variable-1'));
		$exam1->addPatientVariable($this->getReference('patient-variable-1'));

		$manager->persist($exam1);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['exam'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			ExamTypeFixtures::class,
			PatientVariableFixtures::class
		];
	}
}