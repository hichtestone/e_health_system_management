<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\PhaseSetting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PhaseSettingFixtures
 * @package App\ESM\DataFixtures
 */
class PhaseSettingFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$phaseSetting1 = new PhaseSetting();
		$phaseSetting1->setDeletedAt(null);
		$phaseSetting1->setLabel('Baseline');
		$phaseSetting1->setPosition(0);
		$phaseSetting1->setOrdre(1);
		$phaseSetting1->setProject($this->getReference('project-1'));
		$phaseSetting1->setPhaseSettingStatus($this->getReference('phase-setting-status-1'));

		$this->setReference('phase-setting-1', $phaseSetting1);

		$manager->persist($phaseSetting1);
		$manager->flush();

		$phaseSetting2 = new PhaseSetting();
		$phaseSetting2->setDeletedAt(null);
		$phaseSetting2->setLabel('Encryptation');
		$phaseSetting2->setPosition(10);
		$phaseSetting2->setOrdre(2);
		$phaseSetting2->setProject($this->getReference('project-1'));
		$phaseSetting2->setPhaseSettingStatus($this->getReference('phase-setting-status-2'));

		$this->setReference('phase-setting-2', $phaseSetting2);

		$manager->persist($phaseSetting2);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['phaseSetting', 'project'];
	}

	public function getDependencies(): array
	{
		return [
			ProjectFixtures::class,
			PhaseSettingStatusFixtures::class
		];
	}
}