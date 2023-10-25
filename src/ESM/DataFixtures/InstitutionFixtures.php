<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Institution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Cocur\Slugify\Slugify;

/**
 * Class InstitutionFixtures
 * @package App\ESM\DataFixtures
 */
class InstitutionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$slugify = new Slugify();

		$institution1 = new Institution();
		$institution1->setName('institut de la mort qui tue');
		$institution1->setAddress1('1 rue des impasse');
		$institution1->setCity('Bordeaux');
		$institution1->setPostalCode('33000');
		$institution1->setFiness('123245643');
		$institution1->setSiret('34323123432344');
		$institution1->setCountry($this->getReference('country-1'));
		$institution1->setInstitutionType($this->getReference('institutionType-1'));
		$institution1->setSlug($slugify->slugify('institut de la mort qui tue'));
		$institution1->setCountryDepartment($this->getReference('countryDepartment-295'));

		$this->setReference('institution-1', $institution1);

		$manager->persist($institution1);
		$manager->flush();

		$institution2 = new Institution();
		$institution2->setName('institut misanthropomorphique');
		$institution2->setAddress1('3 allée des jonquilles');
		$institution2->setCity('Lyon');
        $institution2->setPostalCode('69001');
		$institution2->setFiness('123245644');
		$institution2->setSiret('32123212345675');
		$institution2->setCountry($this->getReference('country-1'));
		$institution2->setInstitutionType($this->getReference('institutionType-2'));
		$institution2->setSlug($slugify->slugify('institut misanthropomorphique'));
        $institution2->setCountryDepartment($this->getReference('countryDepartment-60'));

		$this->setReference('institution-2', $institution2);

		$manager->persist($institution2);
		$manager->flush();

		$institution3 = new Institution();
		$institution3->setName('EFS institution');
		$institution3->setAddress1('2 boulevard des rues');
		$institution3->setCity('Paris');
        $institution3->setPostalCode('75010');
        $institution3->setFiness('123245645');
		$institution3->setSiret('54398987849576');
		$institution3->setCountry($this->getReference('country-1'));
		$institution3->setInstitutionType($this->getReference('institutionType-3'));
        $institution3->setCountryDepartment($this->getReference('countryDepartment-350'));
		$institution3->setSlug($slugify->slugify('EFS institution'));

		$this->setReference('institution-3', $institution3);

		$manager->persist($institution3);
		$manager->flush();

		$institution4 = new Institution();
		$institution4->setName('institution 2');
		$institution4->setAddress1('10 square des goupilles');
		$institution4->setCity('Marseille');
        $institution4->setPostalCode('13015');
        $institution4->setFiness('123245646');
		$institution4->setSiret('498948274444');
		$institution4->setCountry($this->getReference('country-1'));
		$institution4->setInstitutionType($this->getReference('institutionType-4'));
        $institution4->setCountryDepartment($this->getReference('countryDepartment-455'));
		$institution4->setSlug($slugify->slugify('institution 2'));

		$this->setReference('institution-4', $institution4);

		$manager->persist($institution4);
		$manager->flush();

		$institution5 = new Institution();
		$institution5->setName('hopital international de suede');
		$institution5->setAddress1('12 allée des goupilles');
		$institution5->setCity('Lille');
        $institution5->setPostalCode('59000');
        $institution5->setFiness('123245647');
        $institution5->setSiret('54398987849577');
		$institution5->setCountry($this->getReference('country-14'));
		$institution5->setInstitutionType($this->getReference('institutionType-4'));
		$institution5->setSlug($slugify->slugify('hopital international de suede'));
        $institution5->setCountryDepartment($this->getReference('countryDepartment-410'));

		$this->setReference('institution-5', $institution5);

		$manager->persist($institution5);
		$manager->flush();
	}

     public static function getGroups(): array
     {
         return ['institution', 'center'];
     }

	public function getDependencies(): array
	{
		return [
			CountryFixtures::class,
			CountryDepartmentFixtures::class,
		];
	}
}
