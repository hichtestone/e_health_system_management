<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\TrailType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TrailTypeFixtures
 * @package App\ESM\DataFixtures
 */
class TrailTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$trailTypes = [
			['label' => 'RIPH1', 					'code' => 'RIPH1', 			'ref' => 'trailType-1'],
			['label' => 'RIPH2',  					'code' => 'RIPH2', 			'ref' => 'trailType-2'],
			['label' => 'RIPH3',  					'code' => 'RIPH3', 			'ref' => 'trailType-3'],
			['label' => 'HORS RIPH',  				'code' => 'HORS-RIPH', 		'ref' => 'trailType-4'],
			['label' => 'Recherche biomédicale',  	'code' => 'RECH-BIO-MED', 	'ref' => 'trailType-5'],
			['label' => 'Soins courants',  			'code' => 'SOIN-CRT', 		'ref' => 'trailType-6'],
			['label' => 'Sur données',  			'code' => 'DATA', 			'ref' => 'trailType-7'],
			['label' => 'Collections biologiques',  'code' => 'COLL-BIO', 		'ref' => 'trailType-8'],
		];

		foreach ($trailTypes as $trailType) {

			$entity = new TrailType();
			$entity->setLabel($trailType['label']);
			$entity->setCode($trailType['code']);
			$this->addReference($trailType['ref'], $entity);
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['trailType', 'project', 'prod'];
	}
}
