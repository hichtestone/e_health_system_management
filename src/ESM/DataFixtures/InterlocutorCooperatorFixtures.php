<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Cooperator;
use App\ESM\Entity\DropdownList\ParticipantSpeciality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class InterlocutorCooperatorFixtures
 * @package App\ESM\DataFixtures
 */
class InterlocutorCooperatorFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
        // coopérateur
        $cooperators = [
            'BREAST',
            'EORTC',
            'FFCD',
            'GEFPICS',
            'Génétique et Cancer',
            'GEP',
            'GERCOR',
            'GERICO',
            'GETUG',
            'Groupe Belge',
            'GRT',
            'GSF',
            'GYNECO',
            'IFCT',
            'Médecine Personnalisée',
            'NCCTG',
            'NCIC',
            'ORL',
            'PETACC',
            'POUMON',
            'PRODIGE',
            'SARCOME',
            'SFCE',
            'UCBG',
            'UCGI',
            'BIG',
            'CANTO',
            'UNITRAD',
            'SFRO',
            'ICORG',
            'GRECCAR',
            'SAKK',
            'LLC',
            'IFM',
            'NETSARC',
            'ARCAGY GINECO',
            'TUTHYREF',
            'GCC',
            'SFD',
            'LYSARC',
            'ANOCEF',
            'UNICANCER-AFSOS Soins de Support',
            'AFU',
            'DIALOG',
            'SLO',
            'German Breast Group',
            'IBCSG',
            'TROG Cancer Researchv',
            'EORTC - BCG',
            'ABCSG',
            'GAICO',
            'Success',
            'IOSG',
            'BOOG',
            'HeCOGG',
            'Fondazione Michelangelo',
            'Velindre Hospital',
            'Latin American Cooperative Oncology Group',
            'I . T . M . O . Group(Italian Trials in Medical Oncology)',
            'ANZBCTG',
            'SOLTI',
            'The Icelandic Breast Cancer Group',
            'Hong Kong Breast Oncology Group',
            'SABO',
            'GOCCHI',
            'TCOG',
            'BGICS',
            'GEICAM',
            'NCIC CTG',
            'Hellenic Oncology Research Group',
            'CTRG',
            'GECOPERU',
            'GORTEC',
            'GETTEC',
            'REFCOR',
            'INTERSARC',
        ];

        $refNumber = 1;
        foreach ($cooperators as $item) {
            $entity = new Cooperator();
            $entity->setTitle($item);
            $this->setReference('interlocutorCooperator-'.$refNumber, $entity);
            $refNumber++;
            $manager->persist($entity);
        }
        $manager->flush();
	}

	public static function getGroups(): array
	{
		return ['interlocutorCooperator', 'prod'];
	}
}
