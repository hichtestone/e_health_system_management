<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\ReportBlock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ReportBlockFixtures
 * @package App\ESM\DataFixtures
 */
class ReportBlockFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		// user job
		$reportBlocks = [
			['label' => 'identification', 					'sys' => 1, 'ref' => 'identification'],
			['label' => 'participants', 					'sys' => 1, 'ref' => 'participants'],
			['label' => 'validation', 						'sys' => 1, 'ref' => 'validation'],
			['label' => 'date_of_visits', 					'sys' => 1, 'ref' => 'date_of_visits'],
			['label' => 'documents_discussed', 				'sys' => 1, 'ref' => 'documents_discussed'],
			['label' => 'follow_up', 						'sys' => 1,	'ref' => 'follow_up'],
			['label' => 'close_out', 						'sys' => 1,	'ref' => 'close_out'],
			['label' => 'table_of_patients_notes_checked', 	'sys' => 1, 'ref' => 'table_of_patients_notes_checked'],
			['label' => 'patient_status', 					'sys' => 1, 'ref' => 'patient_status'],
			['label' => 'action_issues_log', 				'sys' => 1, 'ref' => 'action_issues_log'],
			['label' => 'deviations_log', 					'sys' => 1, 'ref' => 'deviations_log'],
		];

		foreach ($reportBlocks as $reportBlock) {
			$entity = new ReportBlock();
			$entity->setName($reportBlock['label']);
			$entity->setSys($reportBlock['sys']);
			$this->setReference($reportBlock['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userProject', 'user', 'project', 'prod'];
	}
}
