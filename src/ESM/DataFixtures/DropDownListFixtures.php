<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\CallProject;
use App\ESM\Entity\DropdownList\CommunicationType;
use App\ESM\Entity\DropdownList\Congress;
use App\ESM\Entity\DropdownList\ContactPhase;
use App\ESM\Entity\DropdownList\ContactType;
use App\ESM\Entity\DropdownList\ContactTypeRecipient;
use App\ESM\Entity\DropdownList\Cooperator;
use App\ESM\Entity\DropdownList\DeviationSample\DecisionTaken;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionCenter;
use App\ESM\Entity\DropdownList\DeviationSample\DetectionContext;
use App\ESM\Entity\DropdownList\DeviationSample\NatureType;
use App\ESM\Entity\DropdownList\DeviationSample\PotentialImpact;
use App\ESM\Entity\DropdownList\DeviationSample\PotentialImpactSample;
use App\ESM\Entity\DropdownList\DeviationSample\ProcessInvolved;
use App\ESM\Entity\DropdownList\Devise;
use App\ESM\Entity\DropdownList\FormalityRule;
use App\ESM\Entity\DropdownList\Funder;
use App\ESM\Entity\DropdownList\IsCongress;
use App\ESM\Entity\DropdownList\Journals;
use App\ESM\Entity\DropdownList\MeetingType;
use App\ESM\Entity\DropdownList\MonitoringReportTemplateType;
use App\ESM\Entity\DropdownList\ParticipantProjectRole;
use App\ESM\Entity\DropdownList\PatientNumber;
use App\ESM\Entity\DropdownList\PaymentUnit;
use App\ESM\Entity\DropdownList\PostType;
use App\ESM\Entity\DropdownList\ProjectDatabaseFreezeReason;
use App\ESM\Entity\DropdownList\RuleTransferTerritory;
use App\ESM\Entity\DropdownList\TrailTreatment;
use App\ESM\Entity\DropdownList\TrainingType;
use App\ESM\Entity\DropdownList\TrlIndice;
use App\ESM\Entity\DropdownList\TypeDeclaration;
use App\ESM\Entity\DropdownList\DeviationType;
use App\ESM\Entity\DropdownList\TypeSubmission;
use App\ESM\Entity\DropdownList\VisitType;
use App\ESM\Entity\PorteeDocumentTransverse;
use App\ESM\Entity\TypeDocumentTransverse;
use App\ESM\Entity\VisitPatientStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class DropDownListFixtures.
 */
class DropDownListFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // participant project role
        foreach ([['name' => 'Interlocuteur', 'code' => 'INTERLOCUTOR'], ['name' => 'Établissement', 'code' => 'INSTITUTION'], ['name' => 'Medicament', 'code' => 'MEDICAMENT']] as $item) {
            $entity = new PorteeDocumentTransverse();
            $entity->setName($item['name']);
            $entity->setCode($item['code']);
            $manager->persist($entity);
        }

        // type document transverse
        foreach (['CV' => 'CV', 'BI' => 'BI', 'RCP' => 'RCP', 'NORMES_LABORATOIRE' => 'Normes de laboratoires'] as $item => $label) {
            $entity = new TypeDocumentTransverse();
            $entity->setName($label);
            $entity->setDaysCountValid(30);
            $entity->setCode($item);
            $manager->persist($entity);
        }

        // Type de soumission
        foreach (['Initiale', 'Amendement', 'Déclaration'] as $item) {
            $entity = new TypeSubmission();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // Type de déclaration
        foreach (['1er patient', 'Fin d\'étude', 'Rapport final'] as $item) {
            $entity = new TypeDeclaration();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // funder
        $funders = [
            'Agendia',
            'Alliance',
            'Amgen',
            'ANR',
            'Aquilab',
            'Astra Zeneca',
            'Bayer',
            'BeiGene',
            'Boehringer Ingelheim',
            'Breast International Group (BIG)',
            'Bristol Myers Squibb',
            'Cancer Research UK',
            'Chugai Pharma',
            'Clovis',
            'Daiichi',
            'DGOS',
            'DGOS-INCa',
            'European Organisation for Research',
            'Ferring Pharmaceuticals A/S',
            'Fondation ARC pour la recherche',
            'Fondation de France',
            'Genentech',
            'Genomic Health',
            'German Breast Group (GBG)',
            'GlaxosmithKline',
            'Grünenthal',
            'HalioDX',
            'Immunomedics',
            'Institut Gustave Roussy',
            'Institut Jules Bordet',
            'Institut National du Cancer (INCa)',
            'Institut of Cancer research (ICR)',
            'International Breast Cancer Study',
            'Ipsen',
            'Ipsogen',
            'Janssen',
            'Kiowarkirin',
            'Ligue Nationale Contre le Cancer',
            'Lilly',
            'Malakoff Mederic',
            'MedImmune',
            'Merck',
            'MSD',
            'Myriad',
            'Nanostring',
            'Natsuca',
            'Nektar',
            'Novartis',
            'Pfizer',
            'Pierre Fabre',
            'Puma',
            'Rhône Poulenc Rorer',
            'Roche',
            'Roche Diagnostics France',
            'SAKK',
            'Sanofi',
            'Sanofi - Aventis',
            'Schweizerische Arbeitsgemeinschaft',
            'Seattle Genetics',
            'Servier',
            'Takeda Pharmaceuticals International',
            'Tesaro',
            'The Royal Marsden NHS Foundation',
            'TheraPanacea',
            'Union Européenne',
            'Zenica',
        ];
        foreach ($funders as $item) {
            $entity = new Funder();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // Devise
        foreach (['€', '$', 'F', '¥', '£'] as $item) {
            $entity = new Devise();
            $entity->setName($item);
            $manager->persist($entity);
        }

        // participant project role
        foreach ([
             ['code' => 'INV', 'label' => 'Investigateur'],
             ['code' => 'TEC', 'label' => 'ARC sur site'],
             ['code' => 'IDR', 'label' => 'Infirmière'],
             ['code' => 'PH', 'label' => 'Pharmacien'],
             ['code' => 'BIO', 'label' => 'Biologiste'],
             ['code' => 'LAB', 'label' => 'Technicien en laboratoire'],
             ['code' => 'PA', 'label' => 'Anatomopathologiste'],
         ] as $item) {
            $entity = new ParticipantProjectRole();
            $entity->setLabel($item['label']);
            $entity->setCode($item['code']);
            $manager->persist($entity);
        }

        // training type
        foreach (['Type A', 'Type B'] as $item) {
            $entity = new TrainingType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // contact type
        foreach (['Téléphone', 'Email', 'Fax', 'Courrier'] as $item) {
            $entity = new ContactType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // contact phase
        foreach (['Pré-selection', 'Suivi'] as $item) {
            $entity = new ContactPhase();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // contact type de destinataire
        foreach (['Interlocuteur(s)', 'Intervenant(s)'] as $item) {
            $entity = new ContactTypeRecipient();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // date raison de l'analyse intermédiaire
        foreach (['IDMC', 'Translationnel', 'DSMB', 'Autre (ex: congrès)'] as $item) {
            $entity = new ProjectDatabaseFreezeReason();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // meeting type
        foreach (['Réunion 1', 'Réunion 2', 'Réunion 3'] as $item) {
            $entity = new MeetingType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // monitoring report type
        $i = 10;
        foreach (['Selected', 'Setting up', 'Monitoring', 'Quality control', 'Closure'] as $item) {
            $entity = new MonitoringReportTemplateType();
            $entity->setLabel($item);
            $entity->setPosition($i);
            $manager->persist($entity);
            $i += 10;
        }

        // Formalité RGPD
        foreach (['MR01', 'MR02', 'MR03', 'MR04', 'MR05', 'MR06', 'Demande d\'autorisation', 'Health Data Hub'] as $item) {
            $entity = new FormalityRule();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // visit type
        foreach (['Screening', 'Inclusion', 'Follow up', 'End of study', 'Extra visit', 'Rando'] as $item) {
            $entity = new VisitType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // visit patient status
        $i = 10;
        foreach ([['label' => 'à monitorer', 'code' => 'S1'], ['label' => 'Monitoré', 'code' => 'S2'], ['label' => 'Non faite', 'code' => 'S3'], ['label' => 'Vide', 'code' => 'S4']] as $item) {
            $entity = new VisitPatientStatus();
            $entity->setLabel($item['label']);
            $entity->setCode($item['code']);
            $entity->setPosition($i);
            $manager->persist($entity);
            $i += 10;
        }

        // indice TRL
        for ($i = 1; $i < 9; ++$i) {
            $entity = new TrlIndice();
            $entity->setLabel($i);
            $manager->persist($entity);
        }

        // unité de paiement
        foreach (['Patient', 'Patient/Visite'] as $item) {
            $entity = new PaymentUnit();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // Publications

        // type de communication
        foreach (['Publication', 'Congrès'] as $item) {
            $entity = new CommunicationType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // si congress
        foreach (['Oral', 'Poster'] as $item) {
            $entity = new IsCongress();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // type de congres
        $congress = [
            'AACR',
            'ABC',
            'ASCO',
            'ASCO GI',
            'ASCO GU',
            'ASH',
            'ASTRO',
            'Autres',
            'Assises de Génétique Humaine et Médicale',
            'BRCA Symposium',
            'CPLF',
            'CTOS',
            'Congrès du collège des humanités médicales (Colhum)',
            'EBCC',
            'ECCO',
            'EHTG',
            'EMSOS',
            'EPICLIN',
            'EQALM',
            'ESGO',
            'ESMO',
            'ESMO IO',
            'ESMO GI',
            'ESTRO',
            'EUROMAR',
            'IAGG',
            'ICCTF',
            'IFCC-EFLM ',
            'ISMRC',
            'MASCC',
            'JFHOD',
            'JDP',
            'SABCS',
            'SFH',
            'SFSPM',
            'SFRO',
            'SIOG',
            'SIOP',
            'SNMMI',
            'SOFOG',
            'UICC',
            'WCLC',
        ];

        foreach ($congress as $item) {
            $entity = new Congress();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // type de revues
        $journals = [
            'Annals of Oncology',
            'Annals of Oncology Advance Acces',
            'Annals of Surgical Oncology',
            'Autres',
            'BMC Cancer',
            'British Medical Journal (BMJ)',
            'Breast Cancer Res',
            'Breast Cancer Research and Treatment',
            'Breast J',
            'British Journal of Cancer',
            'Bull Cancer',
            'Cancer',
            'Cancer Chemother Pharmacol',
            'Cancer Epidemiology',
            'Cancer Epidemiol Biomark. and Prev.',
            'Cancer Genetics',
            'Cancer Medicine',
            'Cancer/Radiotherapie',
            'Cancer Res',
            'Cancérologie digestive',
            'Cells',
            'Cell Death Dis',
            'Clin Breast Cancer',
            'Clin Sarcoma Res',
            'Clinical Colorectal Cancer',
            'Eur J Cancer',
            'Eur J Hum Genet',
            'Eur Urol',
            'Front Oncol',
            'Innovations & Thérapeutiques en Oncologie',
            'Int J Cancer',
            'Int. J. Mol. Sci.',
            'Int J Radiat Oncol Biol Phys',
            'ITO',
            'JAMA Oncol',
            'J Pharm Biomed Annal',
            'Journal of Clinical Oncology',
            'Journal of Medical Case Report',
            'Journal of Pharmacology',
            'JNCCN',
            'JNCI Cancer Spectr',
            'Lancet Oncol',
            'Mol Cancer Research',
            'Med Decis Making',
            'Nat Rev Clin Oncol',
            'Nature',
            'N Engl J Med',
            'OncoImmunology',
            'Oncologie',
            'Oncology',
            'Oncology Hematology',
            'Oncol Ther',
            'Oncotarget',
            'PLoS Med',
            'Psychology, Health & Medicine ',
            'Psycho-Oncology',
            'Radiother Oncol',
            'Scientific Reports',
            'Social Health Illn',
            'Thérapie',
        ];
        foreach ($journals as $item) {
            $entity = new Journals();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // type de publication(sous-type)
        foreach (['Princeps', 'Ancillaire', 'Secondaire'] as $item) {
            $entity = new PostType();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // nom de l'appel à projet
        $callProject = [
            'AcSé',
            'Affectation MERRI',
            'Appel à projet générique',
            'Cohortes',
            'ERA-NET FLAG-ERA',
            'ERA-NET TRANSCAN',
            'ETA-PerMed',
            'H2020',
            'Horizon Europe',
            'PAIR',
            'PHRC-K',
            'PLBIO',
            'PRME',
            'PRT-K',
            'RHU',
            'SHS-E-SP',
            'SIRIC',
            'Autre',
        ];

        foreach ($callProject as $item) {
            $entity = new CallProject();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // type de traitement dans l'étude
        foreach (['Médicament expérimental', 'Médicament auxiliaire', 'Placebo', 'Radiothérapie', 'Chirurgie', 'NA'] as $item) {
            $entity = new TrailTreatment();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // numero patient unique
        foreach (['Par étude', 'par centre'] as $item) {
            $entity = new PatientNumber();
            $entity->setLabel($item);
            $manager->persist($entity);
        }


        // transfert territory
        foreach (['UE', 'Hors UE'] as $item) {
            $entity = new RuleTransferTerritory();
            $entity->setLabel($item);
            $manager->persist($entity);
        }

        // type et subtype  deviation
        $typesDeviation = [
            'IMP' => [
                '01' => [
                    'Supply/storage/retrieval/destruction' => '01.01',
                    'Prescription/administration/compliance' => '01.02',
                    'IMP accountability' => '01.03',
                    'Manufacturing/packaging/labelling' => '01.04',
                ],
            ],
            'IC' => [
                '02' => [
                    'Lack of IC in the site' => '02.01',
                    'IC process' => '02.02',
                    'IC form' => '02.03',
                ],
            ],
            'IEC/IRB' => [
                '03' => [
                    'Lack of IEC/IRB favourable opinion in the site' => '03.01',
                    'Opinion/amendments/notifications to the IEC/IRB' => '03.02',
                    'Composition, functions and operation' => '03.03',
                ],
            ],
            'Subject protection' => [
                '04' => [
                    'Design of the trial' => '04.01',
                    'Personal data protection' => '04.02',
                    'Safeguard of the safety and well-being of subject' => '04.03',
                    'Insurance/indemnity/compensation to subjects' => '04.04',
                    'Payment to trial subjects' => '04.05',
                ],
            ],
            'Regulatory issues' => [
                '05' => [
                    'Lack of regulatory authorities (RA) approval at the site' => '05.01',
                    'Approval/amendments/notifications to the RA' => '05.02',
                    'Manufacturing/importing authorisation' => '05.03',
                ],
            ],
            'Trial management (sponsor)' => [
                '06' => [
                    'Protocol/CRF/diary/questionnaires design' => '06.01',
                    'Data management' => '06.02',
                    'Monitoring' => '06.03',
                    'Audit' => '06.04',
                    'Document control' => '06.05',
                    'Statistical analysis' => '06.06',
                    'CSR' => '06.07',
                ],
            ],
            'Computer system' => [
                '07' => [
                    'Computer validation' => '07.01',
                    'Audit trail and authorised access' => '07.02',
                    'Physical security system and backup' => '07.03',
                ],
            ],
            'Investigational site' => [
                '08' => [
                    'Protocol compliance (selection criteria)' => '08.01',
                    'Protocol compliance (assessment of efficacy)' => '08.02',
                    'Protocol compliance (safety reporting)' => '08.03',
                    'Protocol compliance (others)' => '08.04',
                    'Reporting in CRF/diary' => '08.05',
                ],
            ],
            'Laboratory/technical facilities' => [
                '09' => [
                    'Certification/accreditation' => '09.01',
                    'Assay validation' => '09.02',
                    'Normal values/ranges/updates' => '09.03',
                    'Shipment/storage/labelling/kit samples' => '09.04',
                    'Accountability/traceability of samples' => '09.05',
                    'Analysis/reporting (laboratory)' => '09.06',
                    'Technical validation' => '09.07',
                ],
            ],
            'General' => [
                '10' => [
                    'Organisation and personnel' => '10.01',
                    'Facilities and equipment' => '10.02',
                    'Qualification/training' => '10.03',
                    'SOPs' => '10.04',
                    'Randomisation/blinding/codes IMP' => '10.05',
                    'Source documentation' => '10.06',
                    'Essential documents' => '10.07',
                    'Direct access to data' => '10.08',
                    'Contracts/agreements' => '10.09',
                ],
            ],
            'Others' => [
                '11' => [
                    'other' => '11',
                ],
            ],
        ];

        $i = 10;

        // types parents
        foreach ($typesDeviation as $type => $impact) {
            $deviationType = new DeviationType();
            $deviationType->setType($type);
            $deviationType->setPosition($i);

            foreach ($impact as $code => $children) {
                $deviationType->setCode($code);
                $i += 5;

                $manager->persist($deviationType);

                foreach ($children as $subType => $subCode) {
                    $deviationSubType = new DeviationType();
                    $deviationSubType->setType($subType);
                    $deviationSubType->setCode($subCode);
                    $deviationSubType->setPosition($i);

                    $deviationSubType->setParent($deviationType);

                    $manager->persist($deviationSubType);

                    $i += 5;
                }
            }
        }

		// deviationSample => detectionContext
		$sponsors = [
			'constat terrain',
			'audit interne',
			'incident, accident',
			'réclamation',
			'autre',
		];
		foreach ($sponsors as $item) {
			$entity = new DetectionContext();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		// deviationSample => DetectionCenter
		$sponsors = [
			'centre investigateur',
			'CRB Unicancer',
			'unicancer',
			"plateforme d'analyse",
			'centralisée',
			'transporteur',
			'autre',
		];
		foreach ($sponsors as $item) {
			$entity = new DetectionCenter();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		// deviationSample => ProcessInvolved
		$sponsors = [
			'préparation de l\'expédition',
			'transport',
			'réception',
			'conservation',
			'identification, enregistrement',
			'traitement, transformation',
			'autre',
		];
		foreach ($sponsors as $item) {
			$entity = new ProcessInvolved();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		// deviationSample => NatureType
		$sponsors = [
			'destruction/perte d\'échantillon',
			'excursion de température',
			'conditionnement primaire non conforme',
			'colis non conforme/tubes en vrac',
			'tube(s) manquant(s) ou supplémentaire(s)',
			'problème identification/étiquetage',
			'non-respect des BPL/Lab manuel',
			'consentement non conforme',
			'bordereau de transfert',
			'manquant/non-conforme',
			'autre',
		];
		foreach ($sponsors as $item) {
			$entity = new NatureType();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		// deviationSample => DecisionTaken
		$sponsors = [
			'a placer en attente d\'action corrective',
			'en accord pour conserver/enregistrer/traiter les échantillons',
			'requalification des échantillons',
			'destruction des échantillons',
			'retour des échantillons au centre'
		];
		foreach ($sponsors as $item) {
			$entity = new DecisionTaken();
			$entity->setLabel($item);
			$manager->persist($entity);
		}


		// deviationSample => PotentialImpact
		$sponsors = [
			'IMP',
			'patient confidentiality',
			'patient safety',
			'credibility',
			'autre'
		];
		foreach ($sponsors as $item) {
			$entity = new PotentialImpact();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		// deviationSample => PotentialImpactSample
		$sponsors = [
			'intégrité des échantillons',
			'qualité des données',
			'ethique/règlementaire',
			'autre'
		];
		foreach ($sponsors as $item) {
			$entity = new PotentialImpactSample();
			$entity->setLabel($item);
			$manager->persist($entity);
		}

		$manager->flush();
    }

	public static function getGroups(): array
	{
		return ['prod'];
	}
}
