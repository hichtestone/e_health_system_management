<?php

namespace App\ETMF\DataFixtures;

use App\ETMF\Entity\Artefact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class ArtefactFixtures
 * @package App\ETMF\DataFixtures
 */
class ArtefactFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
	/**
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager): void
	{
		$nbZones 		 	 = 15;
		$nbSections 		 = 5;
		$nbSectionByZone 	 = 5;
		$nbArtefactBySection = 10;
		$nbMailgroup 		 = 15;

		for ($refZone = 1 ; $refZone <= $nbZones ; $refZone++) {

			for ($refSection = 1; $refSection <= $nbSectionByZone; $refSection++) {

				for ($refArtefact = 1 ; $refArtefact <= $nbArtefactBySection ; $refArtefact++) {

					$faker    = Factory::create('fr_FR');
					$artefact = new Artefact();
					$artefact->setCode($faker->randomDigitNotZero());
					$artefact->setName('Artefact ' . $faker->unique->text($faker->numberBetween(6, 12)) . $refArtefact);
					$artefact->setDelayExpired($faker->numberBetween(1, 15));

					$artefact->setSection($this->getReference('section-' . $refZone . '-' . $refSection));

					for ($k = 1 ; $k < random_int(1, 10) ; $k++) {
						$artefact->addMailgroup($this->getReference('mailgroup-' . random_int(1, $nbMailgroup)));
					}

					$extensions = Artefact::EXTENSIONS;
					$artefact->setExtension($extensions);

					$levelRefs = ['artefact-country', 'artefact-center'];
					$key       = array_rand($levelRefs);

					if ($refArtefact % 2 === 1) {
						$artefact->addArtefactLevel($this->getReference($levelRefs[$key]));
					} else {
						$artefact->addArtefactLevel($this->getReference($levelRefs[0]));
						$artefact->addArtefactLevel($this->getReference($levelRefs[1]));
					}

					$this->setReference('artefact-' . $refArtefact, $artefact);

					$manager->persist($artefact);
					$manager->flush();
				}
			}
		}
	}

	public function getDependencies(): array
	{
		return [
			SectionFixtures::class,
			MailgroupFixtures::class,
            ArtefactLevelFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['zone', 'section', 'mailgroup', 'artefact', 'documentVersion'];
	}
}
