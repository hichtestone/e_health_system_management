<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\NameSubmissionRegulatory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NameSubmissionRegulatoryFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$nameComiteEthique = new NameSubmissionRegulatory();
		$nameComiteEthique->setName('CPP');
		$nameComiteEthique->setTypeSubmissionRegulatory($this->getReference('typeSubmissionRegulatory-1'));
		$nameComiteEthique->setDeadline(40);
		$nameComiteEthique->setDeadlineWithQuestion(40 * 2);

		$manager->persist($nameComiteEthique);
		$manager->flush();

		$nameAutoriteCompetente = new NameSubmissionRegulatory();
		$nameAutoriteCompetente->setName('ANSM');
		$nameAutoriteCompetente->setTypeSubmissionRegulatory($this->getReference('typeSubmissionRegulatory-2'));
		$nameAutoriteCompetente->setDeadline(40);
		$nameAutoriteCompetente->setDeadlineWithQuestion(40 * 2);

		$manager->persist($nameAutoriteCompetente);
		$manager->flush();


		// NameSubmissionRegulatory Than France need --------------------------------------------

		$countries = CountryFixtures::getCountries();

		foreach ($countries as $alpha2Code => $country) {

			$delai = 'BE' === $alpha2Code ? 30 : 40;

			for ($s = 1; $s < 3; ++$s) {

				for ($n = 1; $n < 3; ++$n) {

					$name = new NameSubmissionRegulatory();
					$name->setName("TypeAuthorité$s authorité$n $alpha2Code");
					$name->setTypeSubmissionRegulatory($this->getReference('typeSubmissionRegulatory-'.$alpha2Code));
					$name->setDeadline($delai);
					$name->setDeadlineWithQuestion($delai * 2);

					++$delai;

					$manager->persist($name);
					$manager->flush();
				}
			}
		}
	}

	public static function getGroups(): array
	{
		return ['NameSubmissionRegulatory','prod'];
	}

	public function getDependencies(): array
	{
		return [
			TypeSubmissionRegulatoryFixtures::class
		];
	}
}
