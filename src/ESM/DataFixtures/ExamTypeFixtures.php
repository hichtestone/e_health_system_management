<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ExamType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ExamTypeFixtures
 * @package App\ESM\DataFixtures
 */
class ExamTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$exams = [
			['label' => 'échantillons biologiques',				'code' => 'sample', 					'ref' => 'exam-1'],
			['label' => 'frais anatomopathologique', 			'code' => 'anatomopathological-costs', 	'ref' => 'exam-2'],
			['label' => 'bilan biologique (test de grossesse)', 'code' => 'biology-report', 			'ref' => 'exam-3'],
			['label' => 'examen paraclinique (Imagerie)', 		'code' => 'paraclinical-examination', 	'ref' => 'exam-4'],
			['label' => 'consultation de spécialiste', 			'code' => 'specialist-consultation', 	'ref' => 'exam-5'],
			['label' => 'ECG/FEV', 								'code' => 'ecg-fev', 					'ref' => 'exam-6'],
			['label' => 'frais de transports', 					'code' => 'transport-fee', 				'ref' => 'exam-7'],
		];

		foreach ($exams as $exam) {
			$entity = new ExamType();
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
