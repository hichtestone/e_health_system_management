<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\CenterStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CenterStatusFixtures
 * @package App\ESM\DataFixtures
 */
class CenterStatusFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
        $status = [
            ['label' => 'En Présélection', 'type' => '1', 'ref' => 'centreStatus-1'],
            ['label' => 'Non-sélectionné', 'type' => '1', 'ref' => 'centreStatus-2'],
            ['label' => 'Sélectionné', 'type' => '2', 'ref' => 'centreStatus-3'],
            ['label' => 'Retrait de participation', 'type' => '3', 'ref' => 'centreStatus-4'],
            ['label' => 'Avis favorable des autorités', 'type' => '3', 'ref' => 'centreStatus-5'],
            ['label' => 'Mise en place effectuée', 'type' => '3', 'ref' => 'centreStatus-6'],
            ['label' => 'Activé', 'type' => '3', 'ref' => 'centreStatus-7'],
            ['label' => 'Centre actif', 'type' => '3', 'ref' => 'centreStatus-8'],
            ['label' => 'Centre en suivi', 'type' => '3', 'ref' => 'centreStatus-9'],
            ['label' => 'Centre actif', 'type' => '3', 'ref' => 'centreStatus-10'],
            ['label' => 'A clôturer', 'type' => '3', 'ref' => 'centreStatus-11'],
            ['label' => 'Clôturé', 'type' => '3', 'ref' => 'centreStatus-12'],
        ];

        $i = 10;

        foreach ($status as $s) {
            $centerStatus = new CenterStatus();
            $centerStatus->setLabel($s['label']);
            $centerStatus->setType($s['type']);
            $centerStatus->setPosition($i);
            $this->setReference($s['ref'], $centerStatus);

            $i+=10;

            $manager->persist($centerStatus);

        }

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['centerStatus', 'prod'];
	}
}
