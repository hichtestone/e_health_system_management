<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\CountryDepartment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class CountryDepartmentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$departments = $this->getDepartments();

		$i = 10;
		foreach ($departments as $canceropole => $departmentsSubList) {

			$countryDepartment = new CountryDepartment();
			$countryDepartment->setCountry($this->getReference('country-1'));
			$countryDepartment->setName($canceropole);
			$countryDepartment->setPosition($i);

			$i += 5;

			$manager->persist($countryDepartment);

			// Roles Enfants
			foreach ($departmentsSubList as $codeDepartment => $nameDepartment) {
				$subDepartment = new CountryDepartment();
				$subDepartment->setName($nameDepartment);
				$subDepartment->setCode($codeDepartment);
				$subDepartment->setPosition($i);

				// parent
				$subDepartment->setParent($countryDepartment);

                $this->setReference('countryDepartment-' . $i, $subDepartment);

				$manager->persist($subDepartment);

				$i += 5;
			}
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['countryDepartment', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			CountryFixtures::class,
		];
	}

	private function getDepartments(): array
	{
		return [
			'CLARA' => [
				'01' => 'Ain',
				'03' => 'Allier',
				'07' => 'Ardèche',
				'15' => 'Cantal',
				'26' => 'Drome',
				'38' => 'Isère',
				'42' => 'Loire',
				'43' => 'Haute-Loire',
				'63' => 'Puy-de-Dôme',
				'69' => 'Rhône',
				'73' => 'Savoie',
				'74' => 'Haute Savoie',
			],
			'Grand Est' => [
				'08' => 'Ardennes',
				'10' => 'Aube',
				'21' => 'Côte d\'Or',
				'25' => 'Doubs',
				'39' => 'Jura',
				'51' => 'Marne',
				'52' => 'Haute-Marne',
				'54' => 'Meurthe-et-Moselle',
				'55' => 'Meuse',
				'57' => 'Moselle',
				'58' => 'Nievre',
				'67' => 'Bas Rhin',
				'68' => 'Haut-Rhin',
				'70' => 'Haute Saône',
				'71' => 'Saône-et-Loire',
				'88' => 'Vosges',
				'89' => 'Yonne',
				'90' => 'Belfort',
			],
			'Grand Ouest' => [
				'16' => 'Charente',
				'17' => 'Charente-Maritime',
				'18' => 'Cher',
				'22' => 'Côtes d\'Armor',
				'28' => 'Eure et Loir',
				'29' => 'Finistère',
				'35' => 'Ille et Vilaine',
				'36' => 'Indre',
				'37' => 'Indre et Loire',
				'41' => 'Loir et Cher',
				'44' => 'Loire-Atlantique',
				'45' => 'Loiret',
				'49' => 'Maine-et-Loire',
				'53' => 'Mayenne',
				'56' => 'Morbihan',
				'72' => 'Sarthe',
				'79' => 'Deux Sevres',
				'85' => 'Vendee',
				'86' => 'Vienne',
			],
			'Grand Sud-Ouest' => [
				'09' => 'Ariège',
				'11' => 'Aude',
				'12' => 'Aveyron',
				'19' => 'Correze',
				'23' => 'Creuse',
				'24' => 'Dordogne',
				'30' => 'Gard',
				'31' => 'Haute Garonne',
				'32' => 'Gers',
				'34' => 'Herault',
				'33' => 'Gironde',
				'40' => 'Landes',
				'46' => 'Lot',
				'47' => 'Lot et Garenne',
				'48' => 'Lozère',
				'64' => 'Pyrénées-Atlantiques',
				'65' => 'Hautes-Pyrénées',
				'66' => 'Pyrénées-Orientales',
				'81' => 'Tarn',
				'82' => 'Tarn et Garonne',
				'87' => 'Haute Vienne',
			],
			'Ile-de-France' => [
				'75' => 'Paris',
				'77' => 'Seine et Marne',
				'78' => 'Yvelines',
				'91' => 'Essonne',
				'92' => 'Hauts-de-Seine',
				'93' => 'Seine-Saint-Denis',
				'94' => 'Val-de-Marne',
				'95' => 'Val-d\'Oise',
			],
			'Nord-Ouest' => [
				'02' => 'Aisne',
				'14' => 'Calvados',
				'27' => 'Eure',
				'50' => 'Manche',
				'59' => 'Nord',
				'60' => 'Oise',
				'61' => 'Orne',
				'62' => 'Pas-de-Calais',
				'76' => 'Seine-Maritime',
				'80' => 'Somme',
			],
			'PACA' => [
				'04' => 'Alpes de Haute Provence',
				'05' => 'Hautes Alpes',
				'06' => 'Alpes Maritimes',
				'13' => 'Bouches-du-Rhône',
				'83' => 'Var',
				'84' => 'Vaucluse',
			],
			'Non applicable' => [
				'2A'  => 'Corse-du-Sud',
				'2B'  => 'Haute-Corse',
				'971' => 'Guadeloupe',
				'972' => 'Martinique',
				'973' => 'Guyane',
				'974' => 'Réunion',
				'975' => 'Saint-Pierre et Miquelon',
				'976' => 'Mayotte',
				'977' => 'Saint-Berthélemy',
				'978' => 'Saint-Martin',
				'984' => 'Terres australes et Antartiques Françaises',
				'986' => 'Wallis et Futuna',
				'987' => 'Polynésie Française',
				'988' => 'Nouvelle calédonie',
				'989' => 'Ile de Clipperton',
			],
		];
	}
}
