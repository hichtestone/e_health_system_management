<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ExamStatus;
use App\ESM\Entity\DropdownList\ExamType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ExamStatusFixtures
 * @package App\ESM\DataFixtures
 */
class ExamStatusFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		// exam type
		$exams = [
			['label' => 'examen non fait', 'code' => 'exam-not-done', 'ref' => 'exam-status-1'],
			['label' => 'examen non pris en charge', 'code' => 'exam-not-supported', 'ref' => 'exam-status-2'],
			['label' => 'examens antérieurs à l\'inclusion', 'code' => 'exam-prior-inclusion', 'ref' => 'exam-status-3'],
			['label' => 'examen hors centre investigateur', 'code' => 'exam-out-center', 'ref' => 'exam-status-4'],
			['label' => 'NA', 'code' => 'exam-na', 'ref' => 'exam-status-5'],
		];

		foreach ($exams as $exam) {
			$entity = new ExamStatus();
			$entity->setLabel($exam['label']);
			$entity->setCode($exam['code']);
			$this->setReference($exam['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userProject', 'user', 'project', 'prod'];
	}
}
