<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserDepartmentFixtures
 * @package App\ESM\DataFixtures
 */
class UserDepartmentFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$userDepartment1 = new Department();
		$userDepartment1->setLabel('Compta');
		$userDepartment1->setCode('CPT');

		$manager->persist($userDepartment1);
		$manager->flush();

		$userDepartment2 = new Department();
		$userDepartment2->setLabel('Juridique');
		$userDepartment2->setCode('JRD');

		$manager->persist($userDepartment2);
		$manager->flush();

		$userDepartment3 = new Department();
		$userDepartment3->setLabel('IT');
		$userDepartment3->setCode('IT');

		$manager->persist($userDepartment3);
		$manager->flush();

		$userDepartment4 = new Department();
		$userDepartment4->setLabel('R&D');
		$userDepartment4->setCode('R&D');

		$manager->persist($userDepartment4);
		$manager->flush();

		$userDepartment5 = new Department();
		$userDepartment5->setLabel('Data');
		$userDepartment5->setCode('DTA');

		$manager->persist($userDepartment5);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userDepartment', 'prod'];
	}
}


