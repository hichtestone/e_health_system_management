<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ParticipantJob;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ParticipantJobFixtures
 * @package App\ESM\DataFixtures
 */
class ParticipantJobFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$metiers = [

			['label' => 'Administratif', 		'code' => 'ADM'],
			['label' => 'ARC', 					'code' => 'ARC'],
			['label' => 'ARC Coordonnateur', 	'code' => 'ARC-COORD'],
			['label' => 'Autre', 				'code' => 'ATR'],
			['label' => 'Cadre de Santé', 		'code' => 'CDS'],
			['label' => 'Chef de Projet', 		'code' => 'CDP'],
			['label' => 'Chercheur', 			'code' => 'CHR'],
			['label' => 'Diététicien', 			'code' => 'DIET'],
			['label' => 'Dosimétriste', 		'code' => 'DOSI'],
			['label' => 'Infirmier', 			'code' => 'INF'],
			['label' => 'Informaticien', 		'code' => 'INFO'],
			['label' => 'Manipulateur', 		'code' => 'MANIP'],
			['label' => 'Médecin', 				'code' => 'MED'],
			['label' => 'Médecin chercheur', 	'code' => 'MED-CHR'],
			['label' => 'Pharmacien', 			'code' => 'PHR'],
			['label' => 'Physicien', 			'code' => 'PHY'],
			['label' => 'Secrétaire', 			'code' => 'SEC'],
			['label' => 'Statisticien', 		'code' => 'STA'],
			['label' => 'Study Nurse', 			'code' => 'STU'],
			['label' => 'TRC', 					'code' => 'TRC'],
			['label' => 'Technicien', 			'code' => 'THC'],
			['label' => 'Coordinateur', 		'code' => 'COORD'],
		];

		$refNumber = 1;
		foreach ($metiers as $item) {
			$entity = new ParticipantJob();
			$entity->setLabel($item['label']);
			$entity->setCode($item['code']);
			$this->setReference('participantJob-'.$refNumber, $entity);
			$manager->persist($entity);
			$refNumber++;
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['participantJob', 'prod'];
	}
}
