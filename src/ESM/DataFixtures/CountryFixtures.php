<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CountryFixtures
 * @package App\ESM\DataFixtures
 */
class CountryFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$country = new Country();
		$country->setName('France');
		$country->setCode('FR');
		$country->setNameEnglish('La France');

		$this->addReference('country-1', $country);

		$manager->persist($country);
		$manager->flush();

		// OTHERS countries than France -------------------------------

		$othersCountries = self::getCountries();
		$countryNumberRef = 2;

		foreach ($othersCountries as $alpha2Code => $country) {

			$entity = new Country();
			$entity->setCode($alpha2Code);

			foreach ($country as $countryName => $countryNameEnglish) {
				$entity->setName($countryName);
				$entity->setNameEnglish($countryNameEnglish);
			}

			$this->setReference('country-' . $countryNumberRef, $entity);

			$manager->persist($entity);
			$manager->flush();

			$countryNumberRef++;
		}
	}

	public static function getGroups(): array
	{
		return ['country', 'center', 'institution', 'prod'];
	}

	public static function getCountries(): array
	{
		return [
			'BE' => ['Belgium' => 'La Belgique'],
			'CA' => ['Canada' => 'Le Cananda'],
			'EE' => ['Estonia' => 'L\'Estonie'],
			'DE' => ['Germany' => 'L\'Allemagne'],
			'IE' => ['Ireland' => 'L\'Irlande'],
			'IL' => ['Israel' => 'Israël'],
			'IT' => ['Italy' => 'L\'Italie'],
			'MC' => ['Monaco' => 'Monaco'],
			'PT' => ['Portugal' => 'Le Portugal'],
			'RO' => ['Romania' => 'La Roumanie'],
			'SK' => ['Slovakia' => 'La Slovaquie'],
			'ES' => ['Spain' => 'L\'Espagne'],
			'SE' => ['Sweden' => 'La Suède'],
			'CH' => ['Switzerland' => 'La Suisse'],
			'UK' => ['United Kingdom of Great Britain and Northern Ireland (the)' => 'Royaume-Uni de Grande-Bretagne et d\'Irlande du Nord (le)'],
			'US' => ['United States of America (the)' => 'Les États-Unis d\'Amérique '],
		];
	}
}
