<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\UserJob;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class JobFixtures
 * @package App\ESM\DataFixtures
 */
class JobFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		// user job
		$userJobs = [
			['label' => 'Arc',									'code' => 'ARC',  		'ref' => 'job-1'],
			['label' => 'Arc/Externe',							'code' => 'ARC-EXT',	'ref' => 'job-2'],
			['label' => 'AP', 									'code' => 'AP',  		'ref' => 'job-3'],
			['label' => 'Direction Réglementaire', 				'code' => 'DR',  		'ref' => 'job-4'],
			['label' => 'Rédacteur Médical', 					'code' => 'RM',  		'ref' => 'job-5'],
			['label' => 'CEC', 									'code' => 'CEC', 		'ref' => 'job-6'],
			['label' => 'RPC', 									'code' => 'RPC',  		'ref' => 'job-7'],
			['label' => 'Réglementaire', 						'code' => 'RGL',  		'ref' => 'job-8'],
			['label' => 'Chef De Projet', 						'code' => 'CDP',  		'ref' => 'job-9'],
			['label' => 'CDP echantillon', 						'code' => 'CDP-ECH',  	'ref' => 'job-10'],
			['label' => 'Assistance',			 				'code' => 'ASST',  		'ref' => 'job-11'],
			['label' => 'Assistante de Direction', 				'code' => 'AD',  		'ref' => 'job-12'],
			['label' => 'TEC',		 							'code' => 'TEC',		'ref' => 'job-13'],
			['label' => 'TEC / ARC', 							'code' => 'TEC-ARC',	'ref' => 'job-14'],
			['label' => 'Direction des Opérations Cliniques', 	'code' => 'DOC',  		'ref' => 'job-15'],
			['label' => 'Direction R&D', 						'code' => 'DR&D',  		'ref' => 'job-16'],
			['label' => 'Direction Partenariats', 				'code' => 'DP',  		'ref' => 'job-17'],
			['label' => 'Directrice Adjointe R&D', 				'code' => 'DA-R&D', 	'ref' => 'job-18'],
			['label' => 'ARC Manager', 							'code' => 'ARCM',  		'ref' => 'job-19'],
			['label' => 'Assurance Qualité', 					'code' => 'AQ',  		'ref' => 'job-20'],
			['label' => 'Responsable AQ', 						'code' => 'RAQ',  		'ref' => 'job-21'],
			['label' => 'Responsable de programme', 			'code' => 'RSP-PRG',  	'ref' => 'job-22'],
			['label' => 'Responsable de projet transversaux',	'code' => 'RSP-TRSV',  	'ref' => 'job-23'],
			['label' => 'Coordinateur échantillon',				'code' => 'COORD-ECH', 	'ref' => 'job-24'],
			['label' => 'Ref Déviations',						'code' => 'REFD',  		'ref' => 'job-25'],
			['label' => 'Administrateur',						'code' => 'ADM',  		'ref' => 'job-26'],
			['label' => 'Super Administrateur',					'code' => 'SUP-ADM', 	'ref' => 'job-27'],
		];

		foreach ($userJobs as $job) {

			$entity = new UserJob();
			$entity->setLabel($job['label']);
			$entity->setCode($job['code']);
			$this->setReference($job['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userProject', 'user', 'project', 'recette-light', 'prod'];
	}
}
