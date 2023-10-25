<?php

namespace App\ETMF\DataFixtures;

use App\ESM\DataFixtures\ProjectFixtures;
use App\ESM\DataFixtures\SponsorFixtures;
use App\ESM\DataFixtures\UserFixtures;
use App\ESM\DataFixtures\CenterFixtures;
use App\ESM\Entity\Project;
use App\ETMF\Entity\Artefact;
use App\ETMF\Entity\Document;
use App\ETMF\Entity\DocumentVersion;
use App\ETMF\Entity\Section;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;

/**
 * Class DocumentFixtures
 * @package App\ETMF\DataFixtures
 */
class DocumentFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
	/**
	 * @throws Exception
	 */
	public function load(ObjectManager $manager): void
	{
        $tags = ['tag-1', 'tag-2', 'tag-3', 'tag-4', 'tag-5', 'tag-6', 'tag-7', 'tag-8', 'tag-9', 'tag-10'];

	    for($i = 1; $i < 35; $i++) {

            $tags_keys 	= array_rand($tags, 3);
			$zone 		= $this->getReference('zone-'. random_int(1, 15));
			$section 	= $manager->getRepository(Section::class)->findOneBy(['zone' => $zone]);
			$artefact 	= $manager->getRepository(Artefact::class)->findOneBy(['section' => $section]);
			$sponsor 	= $this->getReference('sponsor-'. random_int(1, 13));
			$project 	= $manager->getRepository(Project::class)->findOneBy(['sponsor' => $sponsor]);

			$document = new Document();
            $document->setDescription('Description document ' . $i);
            $document->setArtefact($artefact);
            $document->setSponsor($sponsor);
            $document->setProject($project);

            // Tags document
            $document->addTag($this->getReference($tags[$tags_keys[0]]));
            $document->addTag($this->getReference($tags[$tags_keys[1]]));
            $document->addTag($this->getReference($tags[$tags_keys[2]]));

            // Countries document
            if ($artefact !== null && $project !== null) {
                if ($artefact->getArtefactLevels() !== null) {
                    $artefactLevels = $artefact->getArtefactLevels()->toArray();
                    if (count($artefactLevels) === 2) {
                        $key = rand(0, 1);
                        if ($key == '0') {
                            // level = pays
                            // les pays du projet
                            if (null !== $project->getCountries()) {
                                $countries = $project->getCountries()->toArray();
                                foreach ($countries as $country) {
                                    $document->addCountry($country);
                                }
                            }
                        } else {
                            // level = center
                            // les centre de projet
                            if (null !== $project->getCenters()) {
                                $centres = $project->getCenters()->toArray();
                                foreach ($centres as $centre) {
                                    $document->addCenter($centre);
                                }
                            }
                        }
                    } else {
                        if ( $artefactLevels[0] == '0') {
                            // level = pays
                            if (null !== $project->getCountries()) {
                                $countries = $project->getCountries()->toArray();
                                foreach ($countries as $country) {
                                    $document->addCountry($country);
                                }
                            }
                        } else {
                            // level = center
                            if (null !== $project->getCenters()) {
                                $centres = $project->getCenters()->toArray();
                                foreach ($centres as $centre) {
                                    $document->addCenter($centre);
                                }
                            }
                        }
                    }
                }
            }

            $this->setReference('document-' . $i, $document);

            $manager->persist($document);
            $manager->flush();

            $version = new DocumentVersion();
            $version->setDocument($document);
            $version->setNumberVersion(1);
            $version->setFile('file name ' . $i);
            $version->setCreatedAt(new DateTime());
            $version->setCreatedBy($this->getReference('user-admin'));
            $version->setAuthor($this->getReference('user-admin'));

            $faker = Factory::create('fr_FR');

            $applicationAt= $faker->dateTimeBetween('-30 days', '+30 days');
            $expiredAt = $faker->dateTimeBetween('-30 days', '+30 days');
            $signedAt= $faker->dateTimeBetween('-30 days', '+30 days');

            // date mise en application
            if (null !== $applicationAt) {
                if (new DateTime() >= $applicationAt) {
                    $version->setStatus(DocumentVersion::STATUS_PUBLISH);
                } else {
                    $version->setStatus(DocumentVersion::STATUS_PENDING);
                }

                $version->setApplicationAt($applicationAt);
            } else {
                $version->setStatus(DocumentVersion::STATUS_PUBLISH);
                $version->setApplicationAt(new DateTime());
            }

            if (null !== $expiredAt) {
                if (new DateTime() == $expiredAt) {
                    $version->setStatus(DocumentVersion::STATUS_OBSOLETE);
                }
            }

            $version->setSignedAt($signedAt);
            $version->setValidatedQaBy($this->getReference('user-admin'));

            // tags version
            $version->addTag($this->getReference($tags[$tags_keys[0]]));
            $version->addTag($this->getReference($tags[$tags_keys[1]]));
            $version->addTag($this->getReference($tags[$tags_keys[2]]));

            $this->setReference('documentVersion-' . $i, $version);

            $manager->persist($version);
            $manager->flush();
        }
	}

	public function getDependencies(): array
	{
		return [
			ZoneFixtures::class,
			SectionFixtures::class,
			ArtefactFixtures::class,
            CenterFixtures::class,
            SponsorFixtures::class,
			ProjectFixtures::class,
            UserFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['document', 'artefact', 'documentVersion'];
	}
}
