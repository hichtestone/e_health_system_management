<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\PhaseSettingStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PhaseSettingFixtures
 * @package App\DataFixtures
 */
class PhaseSettingStatusFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$phaseSettingStatus1 = new PhaseSettingStatus();
		$phaseSettingStatus1->setLabel('entity.PhaseSettingStatus.planned');
		$phaseSettingStatus1->setCode('PLD');
		$this->setReference('phase-setting-status-1', $phaseSettingStatus1);

		$manager->persist($phaseSettingStatus1);
		$manager->flush();

		$phaseSettingStatus2 = new PhaseSettingStatus();
		$phaseSettingStatus2->setLabel('entity.PhaseSettingStatus.screened');
		$phaseSettingStatus2->setCode('SCR');
		$this->setReference('phase-setting-status-2', $phaseSettingStatus2);

		$manager->persist($phaseSettingStatus2);
		$manager->flush();

		$phaseSettingStatus3 = new PhaseSettingStatus();
		$phaseSettingStatus3->setLabel('entity.PhaseSettingStatus.screeningFailure');
		$phaseSettingStatus3->setCode('SCRF');
		$this->setReference('phase-setting-status-3', $phaseSettingStatus3);

		$manager->persist($phaseSettingStatus3);
		$manager->flush();

		$phaseSettingStatus4 = new PhaseSettingStatus();
		$phaseSettingStatus4->setLabel('entity.PhaseSettingStatus.signedICF');
		$phaseSettingStatus4->setCode('SICF');
		$this->setReference('phase-setting-status-4', $phaseSettingStatus4);

		$manager->persist($phaseSettingStatus4);
		$manager->flush();


		$phaseSettingStatus5 = new PhaseSettingStatus();
		$phaseSettingStatus5->setLabel('entity.PhaseSettingStatus.ongoing');
		$phaseSettingStatus5->setCode('ONG');
		$this->setReference('phase-setting-status-5', $phaseSettingStatus5);

		$manager->persist($phaseSettingStatus5);
		$manager->flush();


		$phaseSettingStatus6 = new PhaseSettingStatus();
		$phaseSettingStatus6->setLabel('entity.PhaseSettingStatus.followUp');
		$phaseSettingStatus6->setCode('FLU');
		$this->setReference('phase-setting-status-6', $phaseSettingStatus6);

		$manager->persist($phaseSettingStatus6);
		$manager->flush();


		$phaseSettingStatus7 = new PhaseSettingStatus();
		$phaseSettingStatus7->setLabel('entity.PhaseSettingStatus.completed');
		$phaseSettingStatus7->setCode('CPL');
		$this->setReference('phase-setting-status-7', $phaseSettingStatus7);

		$manager->persist($phaseSettingStatus7);
		$manager->flush();


		$phaseSettingStatus8 = new PhaseSettingStatus();
		$phaseSettingStatus8->setLabel('entity.PhaseSettingStatus.withdrawals');
		$phaseSettingStatus8->setCode('WDW');
		$this->setReference('phase-setting-status-8', $phaseSettingStatus8);

		$manager->persist($phaseSettingStatus8);
		$manager->flush();


		$phaseSettingStatus9 = new PhaseSettingStatus();
		$phaseSettingStatus9->setLabel('entity.PhaseSettingStatus.lostFollowUp');
		$phaseSettingStatus9->setCode('LFLU');
		$this->setReference('phase-setting-status-9', $phaseSettingStatus9);

		$manager->persist($phaseSettingStatus9);
		$manager->flush();

		$phaseSettingStatus10 = new PhaseSettingStatus();
		$phaseSettingStatus10->setLabel('entity.PhaseSettingStatus.eos');
		$phaseSettingStatus10->setCode('EOS');
		$this->setReference('phase-setting-status-10', $phaseSettingStatus10);

		$manager->persist($phaseSettingStatus10);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['prod'];
	}
}