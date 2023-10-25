<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Sponsor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class SponsorFixtures
 * @package App\ESM\DataFixtures
 */
class SponsorFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$sponsors = [
			['label' => 'IBCSG',											'code' => 'ibcsg', 								'ref' => 'sponsor-1'],
			['label' => 'EORTC', 											'code' => 'eortc', 								'ref' => 'sponsor-2'],
			['label' => 'ALLIANCE', 										'code' => 'alliance', 							'ref' => 'sponsor-3'],
			['label' => 'SAKK', 											'code' => 'sakk', 								'ref' => 'sponsor-4'],
			['label' => 'ROCHE', 											'code' => 'roche', 								'ref' => 'sponsor-5'],
			['label' => 'CURIE', 											'code' => 'curie', 								'ref' => 'sponsor-6'],
			['label' => 'GBG', 												'code' => 'gbg', 								'ref' => 'sponsor-7'],
			['label' => 'Promoteur hollandais', 							'code' => 'promoteur-hollandais', 				'ref' => 'sponsor-8'],
			['label' => 'PFIZER', 											'code' => 'pfizer', 							'ref' => 'sponsor-9'],
			['label' => 'Gustave Roussy', 									'code' => 'gustave-roussy', 					'ref' => 'sponsor-10'],
			['label' => 'The Clatterbridge Cancer NHs foundation trust', 	'code' => 'clatterbridge-cancer-foundation', 	'ref' => 'sponsor-11'],
			['label' => 'Centre médical de l’université de Leiden', 		'code' => 'universite-leiden', 					'ref' => 'sponsor-12'],
			['label' => 'UNICANCER', 										'code' => 'unicancer', 							'ref' => 'sponsor-13'],
		];
		
		foreach ($sponsors as $sponsor) {

			$entity = new Sponsor();
			$entity->setLabel($sponsor['label']);
			$entity->setCode($sponsor['code']);
			$this->addReference($sponsor['ref'], $entity);
			$manager->persist($entity);
			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['sponsor', 'trailType', 'project', 'prod'];
	}
}
