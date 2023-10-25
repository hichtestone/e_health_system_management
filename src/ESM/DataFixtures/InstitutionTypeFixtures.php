<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\InstitutionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class JobFixtures
 * @package App\ESM\DataFixtures
 */
class InstitutionTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$institutionType1 = new InstitutionType();
		$institutionType1->setLabel('Hôpitaux Publics');
		$institutionType1->setCode('HPP');

		$this->setReference('institutionType-1', $institutionType1);

		$manager->persist($institutionType1);
		$manager->flush();

		$institutionType2 = new InstitutionType();
		$institutionType2->setLabel('Etablissement privé');
		$institutionType2->setCode('EP');

		$this->setReference('institutionType-2', $institutionType2);

		$manager->persist($institutionType2);
		$manager->flush();

		$institutionType3 = new InstitutionType();
		$institutionType3->setLabel('CLCC');
		$institutionType3->setCode('CLCC');

		$this->setReference('institutionType-3', $institutionType3);

		$manager->persist($institutionType3);
		$manager->flush();

		$institutionType4 = new InstitutionType();
		$institutionType4->setLabel('ESPIC');
		$institutionType4->setCode('ESPIC');

		$this->setReference('institutionType-4', $institutionType4);

		$manager->persist($institutionType4);
		$manager->flush();

		$institutionType5 = new InstitutionType();
		$institutionType5->setLabel('Assistance Publique');
		$institutionType5->setCode('ASSP');

		$this->setReference('institutionType-5', $institutionType5);

		$manager->persist($institutionType5);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['InstitutionType', 'institution', 'prod'];
	}
}
