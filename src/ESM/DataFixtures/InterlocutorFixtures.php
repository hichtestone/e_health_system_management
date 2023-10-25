<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Interlocutor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class JobFixtures
 * @package App\ESM\DataFixtures
 */
class InterlocutorFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$interlocutor1 = new Interlocutor();
		$interlocutor1->setCivility($this->getReference('civility-2'));
		$interlocutor1->setFirstName('John');
		$interlocutor1->setLastName('Doe');
		$interlocutor1->setEmail('john.doe@gmail.com');
		$interlocutor1->setPhone('0758584933');
		$interlocutor1->setRppsNumber('12547896315');
		$interlocutor1->setJob($this->getReference('participantJob-13'));
		$interlocutor1->setRppsNumber('12345687925');
		$interlocutor1->setSpecialtyOne($this->getReference('participantSpeciality-1'));
		$interlocutor1->addCooperator($this->getReference('interlocutorCooperator-1'));
		$interlocutor1->addCooperator($this->getReference('interlocutorCooperator-2'));
		$interlocutor1->addCooperator($this->getReference('interlocutorCooperator-3'));

		$interlocutor1->addInstitution($this->getReference('institution-1'));
		$interlocutor1->addInstitution($this->getReference('institution-2'));
		$interlocutor1->addInstitution($this->getReference('institution-3'));

		$this->setReference('interlocutor-1', $interlocutor1);

		$manager->persist($interlocutor1);
		$manager->flush();

        $interlocutor2 = new Interlocutor();
        $interlocutor2->setCivility($this->getReference('civility-2'));
        $interlocutor2->setFirstName('François');
        $interlocutor2->setLastName('Dupont');
        $interlocutor2->setEmail('francois.dupont@gmail.com');
        $interlocutor2->setPhone('0658584933');
        $interlocutor2->setRppsNumber('57849631402');
        $interlocutor2->setJob($this->getReference('participantJob-13'));
        $interlocutor2->setRppsNumber('12345687925');
        $interlocutor2->setSpecialtyOne($this->getReference('participantSpeciality-3'));
        $interlocutor2->addCooperator($this->getReference('interlocutorCooperator-5'));
        $interlocutor2->addCooperator($this->getReference('interlocutorCooperator-7'));

        $interlocutor2->addInstitution($this->getReference('institution-3'));
        $interlocutor2->addInstitution($this->getReference('institution-4'));

        $this->setReference('interlocutor-2', $interlocutor2);

        $manager->persist($interlocutor2);
        $manager->flush();
	}

	public static function getGroups(): array
	{
		return ['interlocutor'];
	}

	public function getDependencies(): array
	{
		return [
			CivilityFixtures::class,
			CenterFixtures::class,
			JobFixtures::class,
			ParticipantSpecialityFixtures::class,
			InterlocutorCooperatorFixtures::class
		];
	}
}
