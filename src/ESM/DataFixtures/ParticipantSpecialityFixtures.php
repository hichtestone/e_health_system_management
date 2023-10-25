<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\ParticipantSpeciality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ParticipantJobFixtures
 * @package App\ESM\DataFixtures
 */
class ParticipantSpecialityFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager): void
	{
		$participantSpecialities = [
			['label' => 'Assurance', 						'code' => 'ASS'],
			['label' => 'Autorité Compétente', 				'code' => 'AC'],
			['label' => 'Autre', 							'code' => 'AUTR'],
			['label' => 'Cancérologie Cervico-faciale', 	'code' => 'CCF'],
			['label' => 'Cardiologie', 						'code' => 'CARD'],
			['label' => 'Chirurgie', 						'code' => 'CHIRG'],
			['label' => 'Comité Ethique', 					'code' => 'CE'],
			['label' => 'Conditionnement', 					'code' => 'CONDIT'],
			['label' => 'Cytologie', 						'code' => 'CYTO'],
			['label' => 'Data management', 					'code' => 'DM'],
			['label' => 'Dermatologie', 					'code' => 'DERMA'],
			['label' => 'Distribution', 					'code' => 'DISTRI'],
			['label' => 'Endocrinologie', 					'code' => 'ENDO'],
			['label' => 'Gastro-entérologie', 				'code' => 'GASTRO'],
			['label' => 'Génétique', 						'code' => 'GENE'],
			['label' => 'Génomique', 						'code' => 'GENO'],
			['label' => 'Gériatrie', 						'code' => 'GERIA'],
			['label' => 'Groupe Coopérateur', 				'code' => 'GCOOP'],
			['label' => 'Gynécologie Médicale', 			'code' => 'GYNE-MED'],
			['label' => 'Gynécologie Obstétrique', 			'code' => 'GYNE-OBS'],
			['label' => 'Hématologie', 						'code' => 'HEMA'],
			['label' => 'Hépatologie', 						'code' => 'HEPA'],
			['label' => 'Histologie', 						'code' => 'HISTO'],
			['label' => 'Immunologie', 						'code' => 'IMMU'],
			['label' => 'Imprimeur', 						'code' => 'IMP'],
			['label' => 'Industriel', 						'code' => 'INDUS'],
			['label' => 'Institutionnel', 					'code' => 'INSTI'],
			['label' => 'Médecine interne', 				'code' => 'MED-INT'],
			['label' => 'Médecine Nucléaire', 				'code' => 'MED-NUC'],
			['label' => 'Métabolisme',						'code' => 'METABO'],
			['label' => 'Neurologie', 						'code' => 'NEURO'],
			['label' => 'Nutrition', 						'code' => 'NUTRI'],
			['label' => 'Oncologie', 						'code' => 'ONCO'],
			['label' => 'Oncologie Médicale', 				'code' => 'ONCO-MED'],
			['label' => 'ORL', 								'code' => 'ORL'],
			['label' => 'Pédiatrie', 						'code' => 'PEDIA'],
			['label' => 'PharmacoCinétique', 				'code' => 'PHARMA-CINE'],
			['label' => 'Pharmacogénétique', 				'code' => 'PHARMA-GENE'],
			['label' => 'Pharmacologie', 					'code' => 'PHARMACO'],
			['label' => 'Pneumologie', 						'code' => 'PNEU'],
			['label' => 'Production', 						'code' => 'PROD'],
			['label' => 'Protéomique', 						'code' => 'PROTEO'],
			['label' => 'Radiopharmacien', 					'code' => 'RADIOPHARMA'],
			['label' => 'Radiothérapie', 					'code' => 'RADIOTHERA'],
			['label' => 'Randomisation', 					'code' => 'RANDO'],
			['label' => 'Rhumatologie', 					'code' => 'RHUMA'],
			['label' => 'Santé publique et Médecine Soc', 	'code' => 'STE-PUB-MED-SOC'],
			['label' => 'Sénologie', 						'code' => 'SENO'],
			['label' => 'Statistiques', 					'code' => 'STATI'],
			['label' => 'Stomatologie', 					'code' => 'STOMA'],
			['label' => 'Transcriptomique', 				'code' => 'TRANSCRI'],
			['label' => 'Transport', 						'code' => 'TRSP'],
			['label' => 'Urologie', 						'code' => 'URO'],
			['label' => 'Médecine générale',				'code' => 'MED-GEN'],
			['label' => 'CIC', 								'code' => 'CIC'],
			['label' => 'URC', 								'code' => 'URC'],
			['label' => 'Recherche clinique', 				'code' => 'RCHR-CLI'],
			['label' => 'NA', 								'code' => 'NA'],
			['label' => 'Néphrologie', 						'code' => 'NEPHRO'],
			['label' => 'Orthopédie', 						'code' => 'ORTHO'],
			['label' => 'Virologie', 						'code' => 'VIRO'],
		];

        $refNumber = 1;
		foreach ($participantSpecialities as $participantSpeciality) {

			$entity = new ParticipantSpeciality();
			$entity->setLabel($participantSpeciality['label']);
			$entity->setCode($participantSpeciality['code']);
            $this->setReference('participantSpeciality-'.$refNumber, $entity);

			$manager->persist($entity);
			$manager->flush();

            $refNumber++;
		}
	}

	public static function getGroups(): array
	{
		return ['participantSpeciality', 'prod'];
	}
}
