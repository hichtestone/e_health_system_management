<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\TypeSubmissionRegulatory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TypeSubmissionRegulatoryFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$typeComiteEthique = new TypeSubmissionRegulatory();
		$typeComiteEthique->setName('Comité Ethique');
		$typeComiteEthique->setCountry($this->getReference('country-1'));

		$this->setReference('typeSubmissionRegulatory-1', $typeComiteEthique);

		$manager->persist($typeComiteEthique);
		$manager->flush();

		$typeAutoriteCompetente = new TypeSubmissionRegulatory();
		$typeAutoriteCompetente->setName('Autorité compétente');
		$typeAutoriteCompetente->setCountry($this->getReference('country-1'));

		$this->setReference('typeSubmissionRegulatory-2', $typeAutoriteCompetente);

		$manager->persist($typeAutoriteCompetente);
		$manager->flush();

		// others TypeSubmissionRegulatory than France need ----------------------------------

		$countries = CountryFixtures::getCountries();

		$countryNumberRef = 2;
		foreach ($countries as $alpha2Code => $country) {

			for ($s = 1; $s < 3; ++$s) {

				$type = new TypeSubmissionRegulatory();
				$type->setName("TypeAuthorité$s $alpha2Code");
				$type->setCountry($this->getReference('country-'. $countryNumberRef));

				$this->setReference('typeSubmissionRegulatory-'.$alpha2Code, $type);

				$manager->persist($type);
				$manager->flush();
			}

			$countryNumberRef++;
		}
	}

	public static function getGroups(): array
	{
		return ['TypeSubmissionRegulatory', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			CountryFixtures::class,
		];
	}
}
