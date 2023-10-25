<?php

namespace App\ESM\Command;

use App\ESM\Entity\Date;
use App\ESM\Entity\DropdownList\CenterStatus;
use App\ESM\Entity\DropdownList\Cooperator;
use App\ESM\Entity\DropdownList\CrfType;
use App\ESM\Entity\DropdownList\MembershipGroup;
use App\ESM\Entity\DropdownList\ParticipantJob;
use App\ESM\Entity\DropdownList\ParticipantSpeciality;
use App\ESM\Entity\DropdownList\ProjectStatus;
use App\ESM\Entity\DropdownList\Sponsor;
use App\ESM\Entity\DropdownList\Territory;
use App\ESM\Entity\DropdownList\TrailPhase;
use App\ESM\Entity\DropdownList\TrailType;
use App\ESM\Entity\Interlocutor;
use App\ESM\Entity\InterlocutorCenter;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\PhaseSetting;
use App\ESM\Entity\Rule;
use App\ESM\Entity\VariableType;
use App\ESM\Entity\Visit;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\ESM\Entity\Center;
use App\ESM\Entity\Patient;
use App\ESM\Entity\Project;
use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Country;
use App\ESM\Entity\DropdownList\CountryDepartment;
use App\ESM\Entity\DropdownList\Department;
use App\ESM\Entity\DropdownList\Society;
use App\ESM\Entity\DropdownList\UserJob;
use App\ESM\Entity\Institution;
use App\ESM\Entity\Profile;
use App\ESM\Entity\Service;
use App\ESM\Entity\DropdownList\InstitutionType;
use App\ESM\Entity\User;
use App\ESM\Service\Mailer\MailerService;
use Cocur\Slugify\Slugify;

/**
 * Class ImportInstitutionCommand
 * @package App\Command
 */
class ImportRepriseCommand extends Command
{
	public const TYPE_REPRISE_INSTITUTION 			 = 'institution';
	public const TYPE_REPRISE_USER        			 = 'user';
	public const TYPE_REPRISE_PROJECT     			 = 'project';
	public const TYPE_REPRISE_INTERLOCUTOR           = 'interlocutor';
	public const TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL = 'interlocutor-principal';
	public const TYPE_REPRISE_PATIENT_VARIABLE       = 'patient-variable';
	public const TYPE_REPRISE_PATIENT_VISIT    		 = 'patient-visit';
	public const TYPE_REPRISE_CENTER    		 	 = 'center';

	private $em;

	private $importFileReprisePath;

	private $importFileRepriseArchivePath;

	private $importFileRepriseErrorPath;

	private $logger;

	private $mailer;

	private $notificationEmail;

	private $projects = [];

	private $projectStatus = [];

	private $sponsors = [];

	private $trailPhases = [];

	private $trailTypes = [];

	private $crfTypes = [];

	private $memberShipGroups = [];

	private $users = [];

	private $patients = [];

	private $centers = [];

	private $institutions = [];

	private $institutionsTmp = [];

	private $foreignInstitutions = [];

	private $institutionTypes = [];

	private $countries = [];

	private $participantJob = [];

	private $countryDepartments = [];

	private $civilities = [];

	private $societies = [];

	private $userDepartments = [];

	private $userJob = [];

	private $profiles = [];

	private $errors = [];

	private $phaseSettings = [];

	private $interlocutors = [];

	private $variablesTypes = [];

	private $services = [];

	private $visits = [];

	private $patientVariables = [];

	protected function configure(): void
	{
		$this
			->setName('import:reprise')
			->setDescription('Importer de données pour application');
	}

	/**
	 * ImportRepriseCommand constructor.
	 * @param EntityManagerInterface $em
	 * @param ParameterBagInterface $parameters
	 * @param LoggerInterface $repriseLogger
	 * @param MailerService $mailer
	 */
	public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters, LoggerInterface $repriseLogger, MailerService $mailer)
	{
		parent::__construct();

		$this->importFileReprisePath        = $parameters->get('IMPORTS_REPRISE_PATH');
		$this->importFileRepriseArchivePath = $parameters->get('IMPORTS_REPRISE_ARCHIVE_PATH');
		$this->importFileRepriseErrorPath   = $parameters->get('IMPORTS_REPRISE_ERROR_PATH');
		$this->notificationEmail            = $parameters->get('email.notification.adress');
		$this->em                           = $em;
		$this->logger                       = $repriseLogger;
		$this->mailer                       = $mailer;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError|ORMException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$ioFiles = new SymfonyStyle($input, $output);
		$ioFiles->title(' ****** Start process IMPORT Reprise ****** ');

		$processFiles = $this->getFiles();

		foreach ($processFiles as $keyFile => $processFile) {

			$ioFiles->newLine();
			$ioFiles->section('IMPORT fichier : ' . $processFile['filename'] . ' (type : ' . $processFile['type'] . ')');

			$this->logger->info('Process file : ' . $processFile['filename']);

			if (!$extractedDatas = $this->extractData($processFile['filename'], $processFile['type'])) {
				continue;
			}

			$ioLines = new SymfonyStyle($input, $output);
			$ioLines->progressStart(count($extractedDatas));

			foreach ($extractedDatas as $keyLine => $extractedData) {

				$row = $keyLine + 2;

				switch ($processFile['type']) {

					case self::TYPE_REPRISE_INSTITUTION:

						if (!$this->createServiceData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_USER:

						if (!$this->createUserData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_PROJECT:

						if (!$this->createProjectData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_INTERLOCUTOR:

						if (!$this->createInterlocutorData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL:

						if (!$this->enablePrincipalInterlocutorData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_PATIENT_VARIABLE:

						$this->loadVariableTypeEntities();

						if (!$this->createPatientVariableData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_PATIENT_VISIT:

						if (!$this->createVisitData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;

					case self::TYPE_REPRISE_CENTER:

						if (!$this->createCenterData($extractedData, $processFile['filename'], $row)) {
							continue 2;
						}
						break;
				}

				$ioLines->progressAdvance();
			}

			$ioLines->progressFinish();

			$this->logger->info('Process file : ' . $processFile['filename'] . ' successful !');
			$this->moveToArchiveDirectory($processFile['filename']);
		}

		if (count($this->errors) > 0) {
			$this->sendEmailNotifications();
			$ioFiles->success('import terminé avec erreurs (detail fichier log)');
		} else {
			$ioFiles->success('validation success');
		}

		return 0;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return Institution|null
	 * @throws ORMException
	 */
	private function createInstitutionData($extractedData, $processFile, $row): ?Institution
	{
		$slugify 		 = new Slugify();
		$institutionType = $this->getInstitutionType($extractedData['TYPE_CODE']);
		$country         = $this->getCountry($extractedData['PAYS']);

		if ($country && $extractedData['PAYS'] === 'FR') {
			$department  = $this->getCountryDepartment($extractedData['DEPARTEMENT_NB']);
		} else {
			$department = null;
		}

		if (!$institutionType || ($extractedData['PAYS'] === 'FR' && !$department) || !$country) {
			return null;
		}

		if ($extractedData['FINESS'] !== '0') {
			$institution = $this->getInstitution($extractedData['FINESS'], true);
		} else {
			$institution = $this->getInstitutionTmp($extractedData['FINESS_TMP'], true);
		}

		try {

			$newInstitution = $institution ?? new Institution();

			$newInstitution->setName($extractedData['NOM']);
			$newInstitution->setInstitutionType($institutionType);
			$newInstitution->setCountryDepartment($department);
			$newInstitution->setAddress1($extractedData['ADRESSE']);
			$newInstitution->setPostalCode($extractedData['CODE_POSTAL']);
			$newInstitution->setCity($extractedData['VILLE']);
			$newInstitution->setCountry($country);
			$newInstitution->setFiness($extractedData['FINESS']);
			$newInstitution->setFinessTmp($extractedData['FINESS_TMP']);
			$newInstitution->setSlug($slugify->slugify($extractedData['NOM']));
			$newInstitution->setSiret($extractedData['SIRET']);
			$newInstitution->setPhone($extractedData['TELEPHONE']);
			$newInstitution->setFax($extractedData['FAX']);

			$this->em->persist($newInstitution);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Institution entity with' .
				' type institution : ' . $extractedData['TYPE_CODE'] .
				' and country : ' . $extractedData['PAYS'] .
				' and department : ' . $extractedData['DEPARTEMENT_NB'] .
				' detail message : ' . $exception->getMessage()
			);

			$this->errors[]
				= 'error persist Institution entity with ' . "<br>" .
				' type institution :' . $extractedData['TYPE_CODE'] . "<br>" .
				' and country : ' . $extractedData['PAYS'] . "<br>" .
				' and department : ' . $extractedData['DEPARTEMENT_NB'] . "<br>" .
				' detail message : ' . $exception->getMessage() . "<br>";

			return null;
		}

		return $newInstitution;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return bool
	 * @throws ORMException
	 */
	private function createServiceData($extractedData, $processFile, $row): ?bool
	{
		$slugify 	 = new Slugify();
		$institution = $this->createInstitutionData($extractedData, $processFile, $row);

		if (!$institution) {
			return false;
		}

		try {

			$service = $this->getService($extractedData['SERVICE'], $institution, true);
			$newService = $service ?? new Service();

			$newService->setName($extractedData['SERVICE']);
			$newService->setAddressInherited($extractedData['ADRESSE_HERITE']);
			$newService->setInstitution($institution);
			$newService->setSlug($slugify->slugify($extractedData['SERVICE']));

			$this->em->persist($newService);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Service entity with ' .
				'institution :' 		. $institution->getName() .
				' and service : ' 		. $extractedData['SERVICE'] .
				' detail message : ' 	. $exception->getMessage()
			);

			$this->errors[]
				= 'error persist Service entity with ' . "<br>" .
				' institution :' 		. $institution->getName() . "<br>" .
				' and service : ' 		. $extractedData['SERVICE'] . "<br>" .
				' detail message : ' 	. $exception->getMessage() . "<br>";

			return null;
		}

		return true;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return bool|null
	 * @throws ORMException
	 */
	private function createCenterData($extractedData, $processFile, $row): ?bool
	{
		$project 	  = $this->getProject($extractedData['ACRONYME']);
		$centerStatus = $this->em->getRepository(CenterStatus::class)->findOneBy(['label' => 'Sélectionné']);

		if (!$project) {
			return false;
		}

		try {

			$center 	= $this->getCenter($extractedData['NUMERO']);
			$newCenter  = $center ?? new Center();

			$newCenter->setName($extractedData['NOM']);
			$newCenter->setCenterStatus($centerStatus);
			$newCenter->setNumber($extractedData['NUMERO']);
			$newCenter->setProject($project);

			$this->em->persist($newCenter);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Center entity with ' 		.
				'project :' 			. $project->getName() 		.
				' and center : ' 		. $extractedData['NOM'] 	.
				' detail message : ' 	. $exception->getMessage()
			);

			$this->errors[]
				= 'error persist Center entity with ' 				. "<br>" .
				' project :' 			. $project->getName() 		. "<br>" .
				' and center : ' 		. $extractedData['NOM'] 	. "<br>" .
				' detail message : ' 	. $exception->getMessage() 	. "<br>";

			return null;
		}

		return true;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return bool
	 * @throws ORMException
	 */
	private function createUserData($extractedData, $processFile, $row): ?bool
	{
		$civility       = $this->getCivility($extractedData['CIVILITE']);
		$society        = $this->getSociety($extractedData['SOCIETE']);
		$userDepartment = $this->getUserDepartment($extractedData['DEPARTEMENT']);
		$userJob        = $this->getUserJob($extractedData['FONCTION']);
		$profile        = $this->getProfile($extractedData['PROFIL']);

		if (!$society || !$userDepartment || !$userJob || !$profile) {
			return false;
		}

		try {

			$user = $this->getUser($extractedData['EMAIL'], true);
			$newUser = $user ?? new User();

			$newUser->setCivility($civility);
			$newUser->setFirstName($extractedData['PRENOM']);
			$newUser->setLastName($extractedData['NOM']);
			$newUser->setPhone($extractedData['TELEPHONE']);
			$newUser->setEmail($extractedData['EMAIL']);
			$newUser->setSociety($society);
			$newUser->setHasAccessEsm($extractedData['ESM']);
			$newUser->setHasAccessEtmf($extractedData['ETMF']);
			$newUser->setDepartment($userDepartment);
			$newUser->setJob($userJob);
			$newUser->setProfile($profile);

			$this->em->persist($newUser);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist User entity with' .
				' firstName :' 				. $extractedData['PRENOM'] .
				' and Lastname : ' 			. $extractedData['NOM'] .
				' and society : ' 			. $society->getName() .
				' and detail message : ' 	. $exception->getMessage()
			);

			$this->errors[]
				= 'error persist User entity with' . "<br>" .
				' firstName : ' 			. $extractedData['PRENOM'] . "<br>" .
				' and Lastname : ' 			. $extractedData['NOM'] . "<br>" .
				' and society : ' 			. $society->getName() . "<br>" .
				' and detail message : ' 	. $exception->getMessage() . "<br>";

			return null;
		}

		return true;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return Project|null
	 * @throws ORMException
	 */
	private function createProjectData($extractedData, $processFile, $row): ?Project
	{
		$projectStatus 	 = $this->getProjectStatus($extractedData['STATUS']);
		$sponsor 		 = $this->getSponsor($extractedData['SPONSOR']);
		$cdp			 = $this->getUser($extractedData['CDP_email']);
		$cec			 = $this->getUser($extractedData['CEC_email']);

		$territory		 = $this->getTerritory($extractedData['TERRITOIRE']);
		$trailPhase		 = $this->getTrailPhase($extractedData['PHASE_ETUDE']);
		$trailType		 = $this->getTrailType($extractedData['TYPE_ESSAI']);
		$membershipGroup = $this->getMembershipGroup($extractedData['GROUPE_APPARTENANCE']);
		$crfType		 = $this->getCrfType($extractedData['CRF_TYPE']);

		$studyPopulation = $extractedData['STUDY_POPULATION'] != "0" ? array_map('intval', explode(",", $extractedData['STUDY_POPULATION'])) : [0];

		if (!$projectStatus || !$sponsor || !$cdp || !$cec || !$territory || !$trailPhase || !$trailType || !$crfType || ! $studyPopulation) {
			return null;
		}

		try {

			$project = $this->getProject($extractedData['ACRONYME'], true);
			$newProject = $project ?? new Project();

			$newProject->setProjectStatus($projectStatus);
			$newProject->setName($extractedData['TITRE_FR']);
			$newProject->setNameEnglish($extractedData['TITRE_EN']);
			$newProject->setAcronyme($extractedData['ACRONYME']);
			$newProject->setRef($extractedData['REFERENCE_INTERNE']);
			$newProject->setSponsor($sponsor);
			$newProject->setResponsiblePM($cdp);
			$newProject->setResponsibleCRA($cec);
			$newProject->setEudractNumber($extractedData['EUDRACT']);
			$newProject->setNctNumber($extractedData['NCT']);
			$newProject->setTerritory($territory);
			$newProject->setAppToken(uniqid('', true));
			$newProject->setCrfType($crfType);
			$newProject->setStudyPopulation($studyPopulation);

			$countriesCode = explode(",", $extractedData['ETUDE_PAYS']);
			foreach ($countriesCode as $countryCode) {
				$country = $this->getCountry($countryCode);
				$newProject->addCountry($country);
			}

			$newProject->setCoordinatingInvestigators($extractedData['COORDINATEUR']);
			$newProject->setTrailPhase($trailPhase);
			$newProject->setTrailType($trailType);

			if ($membershipGroup) {
				$newProject->setMembershipGroup($membershipGroup);
			}

			$this->em->persist($newProject);

            // Create new Date entity
            $date = new Date();
            $date->setProject($newProject);
            $this->em->persist($date);

            // Create new Rule entity
            $rule = new Rule();
            $rule->setProject($newProject);
            $this->em->persist($rule);


			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Project entity with' .
				' acronyme :' 					. $extractedData['ACRONYME'] .
				' and internal reference : ' 	. $extractedData['REFERENCE_INTERNE'] .
				' and sponsor : ' 				. $extractedData['SPONSOR'] .
				' detail message : ' 			. $exception->getMessage()
			);

			$this->errors[] =
				' error persist Project entity with ' 		. "<br>" .
				' acronyme :' 								. $extractedData['ACRONYME'] . "<br>" .
				' and internal reference : ' 				. $extractedData['REFERENCE_INTERNE'] . "<br>" .
				' and sponsor : ' 							. $extractedData['SPONSOR'] . "<br>" .
				' detail message : ' 						. $exception->getMessage() . "<br>";

			return null;
		}

		return $newProject;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return Interlocutor|null
	 * @throws ORMException
	 */
	private function createInterlocutorData($extractedData, $processFile, $row): ?Interlocutor
	{
		$civility 				= $this->getCivility($extractedData['CIVILITE']);
		$job 					= $this->getParticipantJob($extractedData['METIER']);
		$participantSpeciality 	= $this->getParticipantSpeciality($extractedData['SPECIALITE']);

		if ($extractedData['COOPERATEUR'] !== '') {
			$cooperator = $this->getCooperator($extractedData['COOPERATEUR']);
		} else {
			$cooperator = null;
		}

		if ($extractedData['FINESS'] === '' || $extractedData['FINESS'] === '000000000') {
			$institution = $this->getForeignInstitution($extractedData['SLUG ETABLISSEMENT']);
		} else {
			$institution = $this->getInstitution($extractedData['FINESS']);
		}

		try {

			$newInterlocutor = new Interlocutor();
			$newInterlocutor->setCivility($civility);
			$newInterlocutor->setFirstName($extractedData['PRENOM']);
			$newInterlocutor->setLastName($extractedData['NOM']);
			$newInterlocutor->setEmail($extractedData['EMAIL']);
			$newInterlocutor->setPhone($extractedData['TELEPHONE']);
			$newInterlocutor->setFax($extractedData['FAX']);
			$newInterlocutor->setJob($job);
			$newInterlocutor->setSpecialtyOne($participantSpeciality);
			$newInterlocutor->setRppsNumber($extractedData['RPPS']);

			if ($cooperator) {
				$newInterlocutor->addCooperator($cooperator);
			}

			$newInterlocutor->addInstitution($institution);

			$this->em->persist($newInterlocutor);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Interlocutor entity with' 	.
				' firstname :' 										. $extractedData['PRENOM'] 	.
				' and lastname : ' 									. $extractedData['NOM'] 	.
				' and email : ' 									. $extractedData['EMAIL'] 	.
				' detail message : ' 								. $exception->getMessage()
			);

			$this->errors[]
				= 'error persist Interlocutor entity with ' . "<br>" .
				' firstname :' 								. $extractedData['PRENOM'] 	. "<br>" .
				' and lastname : ' 							. $extractedData['NOM'] 	. "<br>" .
				' and email : ' 							. $extractedData['EMAIL'] 	. "<br>" .
				' detail message : ' 						. $exception->getMessage() 	. "<br>" ;

			return null;
		}

		return $newInterlocutor;
	}

	/**
	 * @throws ORMException
	 */
	public function enablePrincipalInterlocutorData($extractedData, $processFile, $row): ?InterlocutorCenter
	{
		$center 	  = $this->getCenter($extractedData['NUMERO_CENTRE']);
		$institution  = $this->getInstitution($extractedData['FINESS']);

		if (!$institution) {
			$institution  = $this->getInstitutionTmp($extractedData['FINESS_TMP']);

			if (!$institution) {
				return null;
			}
		}

		$interlocutor = $this->getInterlocutor($extractedData['PRENOM'], $extractedData['NOM'], $institution);
		$service 	  = $this->getService($extractedData['SERVICE'], $institution);

		if (!$service) {
			return null;
		}

		if (!empty($extractedData['DATE_DEBUT'])) {
			if (!$this->isDateValue($extractedData['DATE_DEBUT'])) {
				return null;
			}
		}

		if (!empty($extractedData['DATE_FIN'])) {
			if (!$this->isDateValue($extractedData['DATE_FIN'])) {
				return null;
			}
		}

		try {

			$interlocutorCenter = new InterlocutorCenter();
			$interlocutorCenter->setCenter($center);
			$interlocutorCenter->setInterlocutor($interlocutor);
			$interlocutorCenter->setService($service);
			$interlocutorCenter->setEnabledAt(new \DateTime());
			$interlocutorCenter->setDisabledAt(null);
			$interlocutorCenter->setMetas([]);
			$interlocutorCenter->setIsPrincipalInvestigator($extractedData['INVESTIGATEUR_PRINCIPAL']);
			$interlocutorCenter->setEnabledAt(\DateTime::createFromFormat('Y-m-d', $extractedData['DATE_DEBUT']));
			$interlocutorCenter->setDisabledAt(\DateTime::createFromFormat('Y-m-d', $extractedData['DATE_FIN']));

			$this->em->persist($interlocutorCenter);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist InterlocutorCenter entity for main interlocutor with' 	.
				' prenom interlocutor :' . $extractedData['PRENOM'] 							.
				' nom interlocutor :' 	 . $extractedData['NOM'] 								.
				' and number center : '  . $extractedData['NUMERO_CENTRE'] 						.
				' detail message : ' 	 . $exception->getMessage()
			);

			$this->errors[]
				= 'error persist InterlocutorCenter entity for main interlocutor with ' 	. "<br>" .
				' prenom interlocutor :'	. $extractedData['PRENOM'] 						. "<br>" .
				' nom interlocutor :'		. $extractedData['NOM'] 						. "<br>" .
				' and number center : '		. $extractedData['NUMERO_CENTRE'] 				. "<br>" .
				' detail message : ' 		. $exception->getMessage() 						. "<br>" ;

			return null;
		}

		return $interlocutorCenter;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return PatientVariable|null
	 * @throws ORMException
	 */
	public function createPatientVariableData($extractedData, $processFile, $row): ?PatientVariable
	{
		$project 	  = $this->getProject($extractedData['ACRONYME']);
		$variableType = $this->variablesTypes[$extractedData['VARIABLE_TYPE']];

		try {

			$patientVariable = $this->getPatientVariable($project, $extractedData['LABEL_VARIABLE']);
			$newPatientVariable = $patientVariable ?? new PatientVariable();

			$newPatientVariable->setProject($project);
			$newPatientVariable->setVariableType($variableType);
			$newPatientVariable->setPosition($extractedData['POSITION']);
			$newPatientVariable->setLabel($extractedData['LABEL_VARIABLE']);
			$newPatientVariable->setSourceId($extractedData['IDENTIFICATION_ECRF']);
			$newPatientVariable->setHasPatient($extractedData['VISIBLE_LIST_PATIENT']);
			$newPatientVariable->setIsVariable(true);

			$this->em->persist($newPatientVariable);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist PatientVariable entity with' 	.
				' label variable :'  . $extractedData['LABEL_VARIABLE'] .
				' project : ' 		 . $extractedData['ACRONYME'] 		.
				' detail message : ' . $exception->getMessage()
			);

			$this->errors[]
				= 'error persist PatientVariable entity with ' 			. "<br>" .
				' label variable :'	 . $extractedData['LABEL_VARIABLE'] . "<br>" .
				' project : '		 . $extractedData['ACRONYME'] 		. "<br>" .
				' detail message : ' . $exception->getMessage() 		. "<br>" ;

			return null;
		}

		return $patientVariable;
	}

	/**
	 * @param $extractedData
	 * @param $processFile
	 * @param $row
	 * @return Visit|null
	 * @throws ORMException
	 */
	public function createVisitData($extractedData, $processFile, $row): ?Visit
	{
		$project 	  = $this->getProject($extractedData['ACRONYME']);
		$phaseSetting = $this->getPhaseSetting($extractedData['PHASE'], $project);

		if (!$phaseSetting || !$project) {
			return null;
		}

		try {

			$visit = $this->getVisit($project, $extractedData['LABEL_VISIT']);
			$newVisit = $visit ?? new Visit();

			$newVisit->setProject($project);
			$newVisit->setLabel($extractedData['LABEL_VISIT']);
			$newVisit->setPosition($extractedData['POSITION']);
			$newVisit->setOrdre($extractedData['ORDRE']);
			$newVisit->setDelay($extractedData['DELAY']);
			$newVisit->setPhase($phaseSetting);
			$newVisit->setPrice($extractedData['PRICE']);
			$newVisit->setShort($extractedData['NOM_COURT_VISIT']);

			$this->em->persist($newVisit);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist Visit entity with' 				.
				' label  :'  		 . $extractedData['LABEL_VISIT'] 	.
				' project : ' 		 . $extractedData['ACRONYME'] 		.
				' detail message : ' . $exception->getMessage()
			);

			$this->errors[]
				= 'error persist Visit entity with ' 				 . "<br>" .
				' label :'	 		 . $extractedData['LABEL_VISIT'] . "<br>" .
				' project : '		 . $extractedData['ACRONYME'] 	 . "<br>" .
				' detail message : ' . $exception->getMessage() 	 . "<br>" ;

			return null;
		}

		return $newVisit;
	}

	/**
	 * @return array
	 */
	private function getFiles(): array
	{
		$files = scandir($this->importFileReprisePath);

		unset($files[array_keys($files, ".")[0]]);
		unset($files[array_keys($files, "..")[0]]);
		unset($files[array_keys($files, "archive")[0]]);
		unset($files[array_keys($files, "error")[0]]);

		if (count($files) < 1) {
			exit;
		}

		foreach ($files as $key => $filename) {

			$n = strrpos($filename,".");
			$fileExtension =  ($n === false) ? "" : substr($filename,$n + 1);

			if ($fileExtension === "csv") {

				if (strpos($filename, self::TYPE_REPRISE_INSTITUTION) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_INSTITUTION, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_USER) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_USER, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_PROJECT) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_PROJECT, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_INTERLOCUTOR) !== false && strpos($filename, self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL) === false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_INTERLOCUTOR, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_PATIENT_VARIABLE) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_PATIENT_VARIABLE, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_PATIENT_VISIT) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_PATIENT_VISIT, 'filename' => $filename];

				} elseif (strpos($filename, self::TYPE_REPRISE_CENTER) !== false) {
					$files[$key] = ['type' => self::TYPE_REPRISE_CENTER, 'filename' => $filename];

				} else {
					unset($files[$key]);
					$this->logger->error('The file : ' . $filename . ' does not have a recognized type of object to import');
					$this->errors[] = 'The file : ' . $filename . ' does not have a recognized type of object to import';
					$this->moveToErrorDirectory($filename);
				}

			} else {
				unset($files[$key]);
				$this->logger->error('The file : ' . $filename . ' does not have a recognized type of extension file to import');
				$this->errors[] = 'The file : ' . $filename . ' does not have a recognized type of extension file to import';
				$this->moveToErrorDirectory($filename);
			}
		}

		return $files;
	}

	/**
	 * @param $filename
	 * @param $type
	 * @return array|false
	 */
	private function extractData($filename, $type)
	{
		$headers           = $this->getHeaders($type);
		$headerMandatories = $this->getMandatoryHeaders($type);
		$nbHeaders         = count($headers);

		$fileHandle     = fopen($this->importFileReprisePath . $filename, "r");
		$extractedDatas = [];

		$lineNumber = 1;
		while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {

			if ($lineNumber === 1) {
				//$dataHeaders = explode(';', $row[0]);
				// test column number
				if (count($row) !== $nbHeaders) {
					$this->logger->error('the number of column of this file : ' . $filename . ' does not match !');
					$this->errors[] = 'the number of column of this file : ' . $filename . ' does not match !';
					$this->moveToErrorDirectory($filename);
					return false;
				}

				// test column name
				$errorColumnName = false;
                $dataHeaders = $row;
				for ($i = 0; $i < $nbHeaders; $i++) {
					if ($dataHeaders[$i] !== $headers[$i]) {

						if ($dataHeaders[$i] === '') {
							$this->logger->error('the header column N°' . ($i + 1) . ' is empty !');
							$this->errors[] = 'the header column N°' . ($i + 1) . ' is empty !';
						} else {
							$this->logger->error('the column ' . $dataHeaders[$i] . ' is not authorize name !');
							$this->errors[] = 'the column ' . $dataHeaders[$i] . ' is not authorize name !';
						}

						$errorColumnName = true;
					}
				}

				if ($errorColumnName) {
					$this->moveToErrorDirectory($filename);
					return false;
				}

			} else {

				//$data             = explode(';', $row[0]);
				$extractedData    = array_combine($headers, $row);
				$errorColumnEmpty = false;

				foreach ($extractedData as $key => $value) {

					if (in_array($key, $headerMandatories, true) && ($value === '' || $value == '' || $value === null)) {
						$this->logger->error('the file ' . $filename . ' contains a empty value at row N°' . $lineNumber . ' column name : ' . $key);
						$this->errors[] = 'the file ' . $filename . ' contains a empty value at row N°' . $lineNumber . ' column name : ' . $key;
						$errorColumnEmpty = true;
					}
				}

				if ($errorColumnEmpty) {
					$this->moveToErrorDirectory($filename);
					return false;
				}

				$extractedDatas[] = $extractedData;
			}

			$lineNumber++;
		}

		return $extractedDatas;
	}

	/**
	 * @param $finess
	 * @param null $createMode
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getInstitution($finess, $createMode = null): ?Institution
	{
		if (!array_key_exists($finess, $this->institutions)) {

			$currentInstitution = $this->em->getRepository(Institution::class)->findOneBy(['finess' => $finess]);

			if ($createMode) {

				if (!$currentInstitution) {
					return null;

				} else {
					$this->institutions[$finess] = $this->em->getReference(Institution::class, $currentInstitution->getId());
					return $currentInstitution;
				}

			} else {

				if (!$currentInstitution) {
					$this->logger->error('the institution with finess number : ' . $finess . ' does not exist');
					$this->errors[] = 'the institution with finess number : ' . $finess . ' does not exist';
					return null;
				} else {
					$this->institutions[$finess] = $this->em->getReference(Institution::class, $currentInstitution->getId());
				}
			}

		} else {
			$currentInstitution = $this->institutions[$finess];
		}

		return $currentInstitution;
	}

	/**
	 * @param $finessTmp
	 * @param null $createMode
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getInstitutionTmp($finessTmp, $createMode = null): ?Institution
	{
		if (!array_key_exists($finessTmp, $this->institutionsTmp)) {

			$currentInstitutionTmp = $this->em->getRepository(Institution::class)->findOneBy(['finessTmp' => $finessTmp]);

			if ($createMode) {

				if (!$currentInstitutionTmp) {
					return null;

				} else {
					$this->institutionsTmp[$finessTmp] = $this->em->getReference(Institution::class, $currentInstitutionTmp->getId());
					return $currentInstitutionTmp;
				}

			} else {

				if (!$currentInstitutionTmp) {
					$this->logger->error('the institution with finess number : ' . $finessTmp . ' does not exist');
					$this->errors[] = 'the institution with finess number : ' . $finessTmp . ' does not exist';
					return null;
				} else {
					$this->institutionsTmp[$finessTmp] = $this->em->getReference(Institution::class, $currentInstitutionTmp->getId());
				}
			}

		} else {
			$currentInstitutionTmp = $this->institutionsTmp[$finessTmp];
		}

		return $currentInstitutionTmp;
	}

	/**
	 * @param $slug
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getForeignInstitution($slug): ?Institution
	{
		if (!array_key_exists($slug, $this->foreignInstitutions)) {

			$currentForeignInstitution = $this->em->getRepository(Institution::class)->findOneBy(['slug' => $slug]);

			if (!$currentForeignInstitution) {
				$this->logger->error('the institution with slug name : ' . $slug . ' does not exist');
				$this->errors[] = 'the institution with slug name : ' . $slug . ' does not exist';
				return null;
			} else {
				$this->foreignInstitutions[$slug] = $this->em->getReference(Institution::class, $currentForeignInstitution->getId());
			}

		} else {
			$currentForeignInstitution = $this->foreignInstitutions[$slug];
		}

		return $currentForeignInstitution;
	}

	/**
	 * @param $email
	 * @param null $createMode
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getUser($email, $createMode = null): ?User
	{
		if (!array_key_exists($email, $this->users)) {

			$currentUser = $this->em->getRepository(User::class)->findOneBy(['email' 	=> $email]);

			if ($createMode) {

				if (!$currentUser) {
					return null;

				} else {
					$this->users[$email] = $this->em->getReference(User::class, $currentUser->getId());
					return $currentUser;
				}

			} else {

				if (!$currentUser) {
					$this->logger->error('the user with email : ' . $email .  ' does not exist');
					$this->errors[] = 'the user with email : ' . $email .  ' does not exist';
					return null;
				} else {
					$this->users[$email] = $this->em->getReference(User::class, $currentUser->getId());
				}
			}

		} else {
			$currentUser = $this->users[$email];
		}

		return $currentUser;
	}

	/**
	 * @param $isFrench
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getTerritory($isFrench): ?Territory
	{
		$label = $isFrench ?? 'International';

		if (!array_key_exists($label, $this->participantJob)) {

			$currentTerritory = $this->em->getRepository(Territory::class)->findOneBy(['label' => $label]);

			if (!$currentTerritory) {
				$this->logger->error('the territory with name : ' . $label .  ' does not exist');
				$this->errors[] = 'the territory with name : ' . $label .  ' does not exist';
				return null;
			} else {
				$this->participantJob[$label] = $this->em->getReference(Territory::class, $currentTerritory->getId());
			}

		} else {
			$currentTerritory = $this->participantJob[$label];
		}

		return $currentTerritory;
	}

	/**
	 * @param $project
	 * @param $label
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getVisit($project, $label): ?Visit
	{
		if (!array_key_exists($label, $this->visits)) {

			$currentVisit = $this->em->getRepository(Visit::class)->findOneBy(['project' => $project, 'label' => $label]);

			if (!$currentVisit) {
				$this->logger->error('the Visit entity with name : ' . $label .  ' does not exist');
				$this->errors[] = 'the Visit entity with name : ' . $label .  ' does not exist';
				return null;
			} else {
				$this->visits[$label] = $this->em->getReference(Visit::class, $currentVisit->getId());
			}

		} else {
			$currentVisit = $this->visits[$label];
		}

		return $currentVisit;
	}

	/**
	 * @param $project
	 * @param $label
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getPatientVariable($project, $label): ?PatientVariable
	{
		if (!array_key_exists($label, $this->patientVariables)) {

			$currentPatientVariable = $this->em->getRepository(PatientVariable::class)->findOneBy(['project' => $project, 'label' => $label]);

			if (!$currentPatientVariable) {
				$this->logger->error('the PatientVariable entity with name : ' . $label .  ' does not exist');
				$this->errors[] = 'the PatientVariable entity with name : ' . $label .  ' does not exist';
				return null;
			} else {
				$this->patientVariables[$label] = $this->em->getReference(PatientVariable::class, $currentPatientVariable->getId());
			}

		} else {
			$currentPatientVariable = $this->patientVariables[$label];
		}

		return $currentPatientVariable;
	}

	/**
	 * @param string $name
	 * @param Institution $institution
	 * @param null $createMode
	 * @return Service|null
	 * @throws ORMException
	 */
	private function getService(string $name, Institution $institution, $createMode = null): ?Service
	{
		if (!array_key_exists($name, $this->services)) {

			$currentService = $this->em->getRepository(Service::class)->findOneBy(['institution' => $institution, 'name' => $name]);

			if ($createMode) {

				if (!$currentService) {
					return null;

				} else {
					$this->services[$name] = $this->em->getReference(Service::class, $currentService->getId());
					return $currentService;
				}

			} else {

				if (!$currentService) {
					$this->logger->error('the service with name : ' . $name .  ' does not exist');
					$this->errors[] = 'the service with name : ' . $name .  ' does not exist';
					return null;
				} else {
					$this->services[$name] = $this->em->getReference(Service::class, $currentService->getId());
				}
			}

		} else {
			$currentService = $this->services[$name];
		}

		return $currentService;
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCivility($code): ?Civility
	{
		if (!array_key_exists($code, $this->civilities)) {

			$currentCivility = $this->em->getRepository(Civility::class)->findOneBy(['code' => $code]);

			if (!$currentCivility) {
				$this->logger->error('the civility with code : ' . $code . ' does not exist');
				$this->errors[] = 'the civility with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->civilities[$code] = $this->em->getReference(Civility::class, $currentCivility->getId());
			}

		} else {
			$currentCivility = $this->civilities[$code];
		}

		return $currentCivility;
	}

	/**
	 * @param $label
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getSociety($label): ?Society
	{
		$label = strtolower($label);

		if (!array_key_exists($label, $this->societies)) {

			$currentSociety = $this->em->getRepository(Society::class)->findOneBy(['name' => $label]);

			if (!$currentSociety) {
				$this->logger->error('the society with name : ' . $label . ' does not exist');
				$this->errors[] = 'the society with name : ' . $label . ' does not exist';
				return null;
			} else {
				$this->societies[$label] = $this->em->getReference(Society::class, $currentSociety->getId());
			}

		} else {
			$currentSociety = $this->societies[$label];
		}

		return $currentSociety;
	}

	/**
	 * @param $prenom
	 * @param $nom
	 * @param $institution
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getInterlocutor($prenom, $nom, $institution): ?Interlocutor
	{
		$slugify 	 		  = new Slugify();
		$prenomSlug  		  = $slugify->slugify($prenom);
		$nomSlug 	 		  = $slugify->slugify($nom);
		$interlocuteurKeySlug = $prenomSlug . '-' . $nomSlug;

		if (!array_key_exists($interlocuteurKeySlug, $this->interlocutors)) {

			$currentInterlocutor = $this->em->getRepository(Interlocutor::class)->getInterlocutorByInstitutionFiness($prenom, $nom, $institution->getFiness());

			if (!$currentInterlocutor) {
				$this->logger->error('the interlocutor with nom : ' . $nom . ' and finess N°:' . $institution->getFiness() . ' does not exist');
				$this->errors[] = 'the interlocutor with nom : ' . $nom . ' and the finess N°:' . $institution->getFiness() . ' does not exist';
				return null;
			} elseif (count($currentInterlocutor) > 1) {
				$this->logger->error('the interlocutor with nom : ' . $nom . ' and finess N°:' . $institution->getFiness() . 'has too many institutions !');
				$this->errors[] = 'the interlocutor with nom : ' . $nom . ' and the finess N°: ' . $institution->getFiness() . 'has too many institutions !';
			}

			else {
				$this->interlocutors[$interlocuteurKeySlug] = $this->em->getReference(Interlocutor::class, $currentInterlocutor[0]->getId());
			}

		} else {
			$currentInterlocutor = $this->interlocutors[$interlocuteurKeySlug];
		}

		return $currentInterlocutor[0];
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getUserDepartment($code): ?Department
	{
		if (!array_key_exists($code, $this->userDepartments)) {

			$currentUserDepartment = $this->em->getRepository(Department::class)->findOneBy(['code' => $code]);

			if (!$currentUserDepartment) {
				$this->logger->error('the user department with code : ' . $code . ' does not exist');
				$this->errors[] = 'the user department with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->userDepartments[$code] = $this->em->getReference(Department::class, $currentUserDepartment->getId());
			}

		} else {
			$currentUserDepartment = $this->userDepartments[$code];
		}

		return $currentUserDepartment;
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getUserJob($code): ?UserJob
	{
		if (!array_key_exists($code, $this->userJob)) {

			$currentUserJob = $this->em->getRepository(UserJob::class)->findOneBy(['code' => $code]);

			if (!$currentUserJob) {
				$this->logger->error('the user job with code : ' . $code . ' does not exist');
				$this->errors[] = 'the user job with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->userJob[$code] = $this->em->getReference(UserJob::class, $currentUserJob->getId());
			}

		} else {
			$currentUserJob = $this->userJob[$code];
		}

		return $currentUserJob;
	}

	/**
	 * @param $acronym
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getProfile($acronym): ?Profile
	{
		if (!array_key_exists($acronym, $this->profiles)) {

			$currentProfile = $this->em->getRepository(Profile::class)->findOneBy(['acronyme' => $acronym]);

			if (!$currentProfile) {
				$this->logger->error('the user profile with acronym : ' . $acronym . ' does not exist');
				$this->errors[] = 'the user profile with acronym : ' . $acronym . ' does not exist';
				return null;
			} else {
				$this->profiles[$acronym] = $this->em->getReference(Profile::class, $currentProfile->getId());
			}

		} else {
			$currentProfile = $this->profiles[$acronym];
		}

		return $currentProfile;
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getInstitutionType($code): ?InstitutionType
	{
		if (!array_key_exists($code, $this->institutionTypes)) {

			$institutionType = $this->em->getRepository(InstitutionType::class)->findOneBy(['code' => $code]);

			if (!$institutionType) {
				$this->logger->error('this institution type with code : ' . $code . ' does not exist');
				$this->errors[] = 'this institution type with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->institutions[$code] = $this->em->getReference(InstitutionType::class, $institutionType->getId());
			}

		} else {
			$institutionType = $this->institutionTypes[$code];
		}

		return $institutionType;
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCountry($code): ?Country
	{
		if (!array_key_exists($code, $this->countries)) {

			$country = $this->em->getRepository(Country::class)->findOneBy(['code' => $code]);

			if (!$country) {
				$this->logger->error('this country with code : ' . $code . ' does not exist');
				$this->errors[] = 'this country with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->countries[$code] = $this->em->getReference(Country::class, $country->getId());
			}

		} else {
			$country = $this->countries[$code];
		}

		return $country;
	}

	/**
	 * @param $number
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCountryDepartment($number): ?CountryDepartment
	{
		if (strlen($number) === 1) {
			$number = '0' . $number;
		}

		if (!array_key_exists($number, $this->countryDepartments)) {

			$department = $this->em->getRepository(CountryDepartment::class)->findOneBy(['code' => $number]);

			if (!$department) {
				$this->logger->error('this department with number : ' . $number . ' does not exist');
				$this->errors[] = 'this department with number : ' . $number . ' does not exist';
				return null;
			} else {
				$this->countryDepartments[$number] = $this->em->getReference(CountryDepartment::class, $department->getId());
			}

		} else {
			$department = $this->countryDepartments[$number];
		}

		return $department;
	}

	/**
	 * @param $acronym
	 * @param null $createMode
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getProject($acronym, $createMode = null): ?Project
	{
		if (!array_key_exists($acronym, $this->projects)) {

			$currentProject = $this->em->getRepository(Project::class)->findOneBy(['acronyme' => $acronym]);

			if ($createMode) {

				if (!$currentProject) {
					return null;

				} else {
					$this->projects[$acronym] = $this->em->getReference(Project::class, $currentProject->getId());
					return $currentProject;
				}

			} else {

				if (!$currentProject) {
					$this->logger->error('the project with acronym : ' . $acronym . ' does not exist');
					$this->errors[] = 'the project with acronym : ' . $acronym . ' does not exist';
					return null;
				} else {
					$this->projects[$acronym] = $this->em->getReference(Project::class, $currentProject->getId());
				}
			}

		} else {
			$currentProject = $this->projects[$acronym];
		}

		return $currentProject;
	}

	/**
	 * @param $code
	 * @return ProjectStatus|null
	 * @throws ORMException
	 */
	private function getProjectStatus($code): ?ProjectStatus
	{
		if (!array_key_exists($code, $this->projectStatus)) {

			$currentProjectStatus = $this->em->getRepository(ProjectStatus::class)->findOneBy(['code' => $code]);

			if (!$currentProjectStatus) {
				$this->logger->error('the project status with code : ' . $code . ' does not exist');
				$this->errors[] = 'the project status with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->projectStatus[$code] = $this->em->getReference(ProjectStatus::class, $currentProjectStatus->getId());
			}

		} else {
			$currentProjectStatus = $this->projectStatus[$code];
		}

		return $currentProjectStatus;
	}

	/**
	 * @param $code
	 * @return Sponsor|null
	 * @throws ORMException
	 */
	private function getSponsor($code): ?Sponsor
	{
		if (!array_key_exists($code, $this->sponsors)) {

			$currentSponsor = $this->em->getRepository(Sponsor::class)->findOneBy(['code' => $code]);

			if (!$currentSponsor) {
				$this->logger->error('the sponsor with code : ' . $code . ' does not exist');
				$this->errors[] = 'the sponsor with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->sponsors[$code] = $this->em->getReference(Sponsor::class, $currentSponsor->getId());
			}

		} else {
			$currentSponsor = $this->sponsors[$code];
		}

		return $currentSponsor;
	}

	/**
	 * @param $code
	 * @return TrailType|null
	 * @throws ORMException
	 */
	private function getTrailType($code): ?TrailType
	{
		if (!array_key_exists($code, $this->trailTypes)) {

			$currentTrailType = $this->em->getRepository(TrailType::class)->findOneBy(['code' => $code]);

			if (!$currentTrailType) {
				$this->logger->error('the trail type with code : ' . $code . ' does not exist');
				$this->errors[] = 'the trail type with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->trailTypes[$code] = $this->em->getReference(TrailType::class, $currentTrailType->getId());
			}

		} else {
			$currentTrailType = $this->trailTypes[$code];
		}

		return $currentTrailType;
	}

	/**
	 * @param $label
	 * @param Project $project
	 * @return PhaseSetting|null
	 * @throws ORMException
	 */
	public function getPhaseSetting($label, Project $project): ?PhaseSetting
	{
		if (!array_key_exists($label, $this->phaseSettings)) {

			$currentPhaseSetting = $this->em->getRepository(PhaseSetting::class)->findOneBy(['project' => $project, 'label' => $label]);

			if (!$currentPhaseSetting) {
				$this->logger->error('the phaseSetting with name : ' . $label .  ' does not exist');
				$this->errors[] = 'the phaseSetting with name : ' . $label .  ' does not exist';
				return null;
			} else {
				$this->phaseSettings[$label] = $this->em->getReference(PhaseSetting::class, $currentPhaseSetting->getId());
			}

		} else {
			$currentPhaseSetting = $this->phaseSettings[$label];
		}

		return $currentPhaseSetting;
	}

	/**
	 * @param $label
	 * @return TrailPhase|null
	 * @throws ORMException
	 */
	private function getTrailPhase($label): ?TrailPhase
	{
		if (!array_key_exists($label, $this->trailPhases)) {

			$currentTrailPhase = $this->em->getRepository(TrailPhase::class)->findOneBy(['label' => $label]);

			if (!$currentTrailPhase) {
				$this->logger->error('the trail phase with code : ' . $label . ' does not exist');
				$this->errors[] = 'the trail phase with code : ' . $label . ' does not exist';
				return null;
			} else {
				$this->trailPhases[$label] = $this->em->getReference(TrailPhase::class, $currentTrailPhase->getId());
			}

		} else {
			$currentTrailPhase = $this->trailPhases[$label];
		}

		return $currentTrailPhase;
	}

	/**
	 * @param $label
	 * @return MembershipGroup|null
	 * @throws ORMException
	 */
	private function getMembershipGroup($label): ?MembershipGroup
	{
		if (!array_key_exists($label, $this->memberShipGroups)) {

			$currentMembershipGroup = $this->em->getRepository(MembershipGroup::class)->findOneBy(['label' => $label]);

			if (!$currentMembershipGroup) {
				$this->logger->error('the membership group with code : ' . $label . ' does not exist');
				$this->errors[] = 'the membership group with code : ' . $label . ' does not exist';
				return null;
			} else {
				$this->memberShipGroups[$label] = $this->em->getReference(MembershipGroup::class, $currentMembershipGroup->getId());
			}

		} else {
			$currentMembershipGroup = $this->memberShipGroups[$label];
		}

		return $currentMembershipGroup;
	}

	/**
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getParticipantSpeciality($code): ?ParticipantSpeciality
	{
		if (!array_key_exists($code, $this->participantJob)) {

			$currentParticipantSpeciality = $this->em->getRepository(ParticipantSpeciality::class)->findOneBy(['code' => $code]);

			if (!$currentParticipantSpeciality) {
				$this->logger->error('the speciality with code : ' . $code .  ' does not exist');
				$this->errors[] = 'the speciality with code : ' . $code .  ' does not exist';
				return null;
			} else {
				$this->participantJob[$code] = $this->em->getReference(ParticipantSpeciality::class, $currentParticipantSpeciality->getId());
			}

		} else {
			$currentParticipantSpeciality = $this->participantJob[$code];
		}

		return $currentParticipantSpeciality;
	}

	/**
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getParticipantJob($code): ?ParticipantJob
	{
		if (!array_key_exists($code, $this->participantJob)) {

			$currentParticipantJob = $this->em->getRepository(ParticipantJob::class)->findOneBy(['code' => $code]);

			if (!$currentParticipantJob) {
				$this->logger->error('the job with code : ' . $code .  ' does not exist');
				$this->errors[] = 'the job with code : ' . $code .  ' does not exist';
				return null;
			} else {
				$this->participantJob[$code] = $this->em->getReference(ParticipantJob::class, $currentParticipantJob->getId());
			}

		} else {
			$currentParticipantJob = $this->participantJob[$code];
		}

		return $currentParticipantJob;
	}

	/**
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCooperator($title): ?Cooperator
	{
		if (!array_key_exists($title, $this->participantJob)) {

			$currentCooperator = $this->em->getRepository(Cooperator::class)->findOneBy(['title' => $title]);

			if (!$currentCooperator) {
				$this->logger->error('the cooperator with title : ' . $title .  ' does not exist');
				$this->errors[] = 'the cooperator with title : ' . $title .  ' does not exist';
				return null;
			} else {
				$this->participantJob[$title] = $this->em->getReference(Cooperator::class, $currentCooperator->getId());
			}

		} else {
			$currentCooperator = $this->participantJob[$title];
		}

		return $currentCooperator;
	}

	/**
	 * @param $code
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCrfType($code): ?CrfType
	{
		if (!array_key_exists($code, $this->crfTypes)) {

			$currentCrfType = $this->em->getRepository(CrfType::class)->findOneBy(['code' => $code]);

			if (!$currentCrfType) {
				$this->logger->error('the CRFType with code : ' . $code . ' does not exist');
				$this->errors[] = 'the CRFType with code : ' . $code . ' does not exist';
				return null;
			} else {
				$this->crfTypes[$code] = $this->em->getReference(CrfType::class, $currentCrfType->getId());
			}

		} else {
			$currentCrfType = $this->crfTypes[$code];
		}

		return $currentCrfType;
	}

	/**
	 * @param $centerNumber
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getCenter($centerNumber): ?Center
	{
		if (!array_key_exists($centerNumber, $this->centers)) {
			$currentCenter = $this->em->getRepository(Center::class)->findOneBy(['number' => $centerNumber]);

			if (!$currentCenter) {
				$this->logger->error('the center with number : ' . $centerNumber . ' does not exist');
				$this->errors[] = 'the center with number : ' . $centerNumber . ' does not exist';
				return null;
			} else {
				$this->centers[$centerNumber] = $this->em->getReference(Center::class, $currentCenter->getId());
			}

		} else {
			$currentCenter = $this->centers[$centerNumber];
		}

		return $currentCenter;
	}

	/**
	 * @param $patientNumber
	 * @param $center
	 * @param $project
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getPatient($patientNumber, $center, $project): ?Patient
	{
		if (!array_key_exists($patientNumber, $this->patients)) {

			$currentPatient = $this->em->getRepository(Patient::class)->findOneBy([
				'project' => $project->getId(),
				'center'  => $center->getId(),
				'number'  => $patientNumber,
			]);

			if (!$currentPatient) {

				// if import declare patient number and does not exist THEN create this !
				$newPatient = new Patient();
				$newPatient->setNumber($patientNumber);
				$newPatient->setProject($project);
				$newPatient->setCenter($center);
				$this->em->persist($newPatient);
				$this->em->flush();

				$currentPatient = $this->em->getReference(Patient::class, $newPatient->getId());

			} else {
				$this->patients[$patientNumber] = $this->em->getReference(Patient::class, $currentPatient->getId());
				$currentPatient                 = $this->patients[$patientNumber];
			}

		} else {
			$currentPatient = $this->patients[$patientNumber];
		}

		return $currentPatient;
	}

	/**
	 * permet de recuperer en BDD directement l'ensemble des types de variables en 1 fois
	 */
	public function loadVariableTypeEntities(): void
	{
		$this->variablesTypes['numeric'] = $this->em->getRepository(VariableType::class)->findOneBy(['label' => 'numeric']);
		$this->variablesTypes['date'] 	 = $this->em->getRepository(VariableType::class)->findOneBy(['label' => 'date']);
		$this->variablesTypes['string']  = $this->em->getRepository(VariableType::class)->findOneBy(['label' => 'string']);
		$this->variablesTypes['list'] 	 = $this->em->getRepository(VariableType::class)->findOneBy(['label' => 'list']);
	}

	/**
	 * @param $filename
	 */
	private function moveToArchiveDirectory($filename): void
	{
		$originFilePath      = $this->importFileReprisePath . $filename;
		$destinationFilePath = $this->importFileRepriseArchivePath . $filename;

		if (!copy($originFilePath, $destinationFilePath)) {
			$this->logger->error('error to  copy this file : ' . $filename . ' to archive directory');
		}

		unlink($originFilePath);
	}

	/**
	 * @param $filename
	 */
	private function moveToErrorDirectory($filename): void
	{
		$originFilePath      = $this->importFileReprisePath . $filename;
		$destinationFilePath = $this->importFileRepriseErrorPath . $filename;

		if (!copy($originFilePath, $destinationFilePath)) {
			$this->logger->error('error to  copy this file : ' . $filename . ' to error directory');
		}

		unlink($originFilePath);
	}

	/**
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	private function sendEmailNotifications(): void
	{
		$this->mailer->setLocale('fr');

		$this->mailer->sendEmail('notificationErrorReprise', [
			'errors' => $this->errors
		], 'ESM Unicancer - erreur import reprise', [], [$this->notificationEmail]);

		$this->mailer->resetLocale();
	}

	/**
	 * @param $value
	 * @return void
	 * @throws Exception
	 */
	private function isDateValue($value): bool
	{
		// main general test date format
		if (!is_string($value)) {
			$this->logger->error('Le format de la date est incorrect : la valeur n\'est pas de type string.');
			$this->errors[] = 'Le format de la date est incorrect : la valeur n\'est pas de type string.';
			return false;
		} elseif (strlen($value) !== 10) {
			$this->logger->error('Le format de la date est incorrect : le nombre de caractère doit être de 10.');
			$this->errors[] = 'Le format de la date est incorrect : le nombre de caractère doit être de 10.';
			return false;
		} elseif (strpos($value, '-') === false) {
			$this->logger->error('Le format de la date est incorrect : le caractère de séparation \'-\' est inexistant.');
			$this->errors[] = 'Le format de la date est incorrect : le caractère de séparation \'-\' est inexistant.';
			return false;
		}

		// test ISO date format
		if (!is_numeric($value[0]) || !is_numeric($value[1]) || !is_numeric($value[2]) || !is_numeric($value[3])) {
			$this->logger->error('Le format de la date est incorrect : les 4 premiers caractères ne sont pas numériques');
			$this->errors[] = 'Le format de la date est incorrect : les 4 premiers caractères ne sont pas numériques';
			return false;
		} elseif ($value[4] !== '-' || $value[7] !== '-') {
			$this->logger->error('Le format de la date est incorrect : les caractères de séparations \'-\' sont mal placés.');
			$this->errors[] = 'Le format de la date est incorrect : les caractères de séparations \'-\' sont mal placés.';
			return false;
		} elseif (!is_numeric($value[5]) || !is_numeric($value[6])) {
			$this->logger->error('Le format de la date est incorrect : les caractères du mois ne sont pas numériques.');
			$this->errors[] = 'Le format de la date est incorrect : les caractères du mois ne sont pas numériques.';
			return false;
		} elseif (!is_numeric($value[8]) || !is_numeric($value[9])) {
			$this->logger->error('Le format de la date est incorrect : les caractères du jour ne sont pas numériques.');
			$this->errors[] = 'Le format de la date est incorrect : les caractères du jour ne sont pas numériques.';
			return false;
		}

		// test day - month - year format
		$dateArray = explode('-', $value);
		[$year, $month, $day] = $dateArray;

		if (strlen($year) !== 4) {
			$this->logger->error('Le format de la date est incorrect : année est exprimé avec le mauvais nombre de caractères.');
			$this->errors[] = 'Le format de la date est incorrect : année est exprimé avec le mauvais nombre de caractères.';
			return false;
		} elseif ($year < 1900 || $year > 2300) {
			$this->logger->error('Le format de la date est incorrect : année pas comprise entre 1900 et 2300.');
			$this->errors[] = 'Le format de la date est incorrect : année pas comprise entre 1900 et 2300.';
			return false;
		}

		if (strlen($month) !== 2) {
			$this->logger->error('Le format de la date est incorrect : mois est exprimé avec le mauvais nombre de caractères.');
			$this->errors[] = 'Le format de la date est incorrect : mois est exprimé avec le mauvais nombre de caractères.';
			return false;
		} elseif ($month < 1 || $month > 12) {
			$this->logger->error('Le format de la date est incorrect : mois pas compris entre 1 et 12.');
			$this->errors[] = 'Le format de la date est incorrect : mois pas compris entre 1 et 12.';
			return false;
		}

		if (strlen($day) !== 2) {
			$this->logger->error('Le format de la date est incorrect : jour est exprimé avec le mauvais nombre de caractères.');
			$this->errors[] = 'Le format de la date est incorrect : jour est exprimé avec le mauvais nombre de caractères.';
			return false;
		} elseif ($day < 1 || $day > 31) {
			$this->logger->error('Le format de la date est incorrect : jour pas compris entre 1 et 31.');
			$this->errors[] = 'Le format de la date est incorrect : jour pas compris entre 1 et 31.';
			return false;
		}

		return true;
	}

	/**
	 * @param $type
	 * @return array|string[]
	 */
	private function getHeaders($type): array
	{
		$headers = [];

		switch ($type) {

			case self::TYPE_REPRISE_USER:

				$headers = ['CIVILITE', 'NOM', 'PRENOM', 'TELEPHONE', 'EMAIL', 'SOCIETE', 'ESM', 'ETMF', 'DEPARTEMENT', 'FONCTION', 'PROFIL'];
				break;

			case self::TYPE_REPRISE_INSTITUTION:

				$headers = ['NOM', 'TYPE_CODE', 'DEPARTEMENT_NB', 'ADRESSE', 'CODE_POSTAL', 'VILLE', 'PAYS', 'FINESS', 'FINESS_TMP', 'SIRET', 'TELEPHONE', 'FAX', 'SERVICE', 'ADRESSE_HERITE'];
				break;

			case self::TYPE_REPRISE_PROJECT:

				$headers = ['STATUS', 'TITRE_EN', 'TITRE_FR', 'ACRONYME', 'REFERENCE_INTERNE', 'SPONSOR', 'CDP_email', 'CEC_email', 'EUDRACT', 'NCT', 'TERRITOIRE', 'ETUDE_PAYS', 'COORDINATEUR', 'PHASE_ETUDE', 'GROUPE_APPARTENANCE', 'TYPE_ESSAI', 'CRF_TYPE', 'STUDY_POPULATION'];
				break;

			case self::TYPE_REPRISE_INTERLOCUTOR:

				$headers = ['CIVILITE', 'PRENOM', 'NOM', 'EMAIL', 'TELEPHONE', 'FAX', 'METIER', 'SPECIALITE', 'RPPS', 'COOPERATEUR', 'FINESS', 'SLUG ETABLISSEMENT'];
				break;

			case self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL:

				$headers = ['ACRONYME', 'NUMERO_CENTRE', 'PRENOM', 'NOM', 'INVESTIGATEUR_PRINCIPAL', 'FINESS', 'SERVICE', 'DATE_DEBUT', 'DATE_FIN'];
				break;

			case self::TYPE_REPRISE_PATIENT_VARIABLE:

				$headers = ['ACRONYME', 'VARIABLE_TYPE', 'POSITION', 'LABEL_VARIABLE', 'IDENTIFICATION_ECRF', 'VISIBLE_LIST_PATIENT'];
				break;

			case self::TYPE_REPRISE_PATIENT_VISIT:

				$headers = ['ACRONYME', 'NOM_COURT_VISIT', 'LABEL_VISIT', 'POSITION', 'ORDRE', 'DELAY', 'PHASE', 'PRICE'];
				break;

			case self::TYPE_REPRISE_CENTER:

				$headers = ['ACRONYME', 'NUMERO', 'NOM'];
				break;
		}

		return $headers;
	}

	/**
	 * @param $type
	 * @return array|string[]
	 */
	private function getMandatoryHeaders($type): array
	{
		$headersMandatory = [];

		switch ($type) {

			case self::TYPE_REPRISE_USER:

				$headersMandatory = ['NOM', 'PRENOM', 'EMAIL', 'SOCIETE', 'ESM', 'ETMF', 'DEPARTEMENT', 'FONCTION', 'PROFIL'];
				break;

			case self::TYPE_REPRISE_INSTITUTION:

				$headersMandatory = ['NOM', 'TYPE_CODE', 'DEPARTEMENT_NB', 'ADRESSE', 'CODE_POSTAL', 'VILLE', 'PAYS', 'FINESS', 'SERVICE', 'ADRESSE_HERITE'];
				break;

			case self::TYPE_REPRISE_PROJECT:

				$headersMandatory = ['STATUS', 'TITRE_EN', 'TITRE_FR', 'ACRONYME', 'SPONSOR', 'CDP_email', 'CEC_email', 'TERRITOIRE', 'ETUDE_PAYS', 'PHASE_ETUDE', 'TYPE_ESSAI', 'CRF_TYPE', 'STUDY_POPULATION'];
				break;

			case self::TYPE_REPRISE_INTERLOCUTOR:

				$headersMandatory = ['PRENOM', 'NOM', 'EMAIL', 'TELEPHONE', 'METIER', 'SPECIALITE'];
				break;

			case self::TYPE_REPRISE_INTERLOCUTOR_PRINCIPAL:

				$headersMandatory = ['ACRONYME', 'NUMERO_CENTRE', 'PRENOM', 'NOM', 'INVESTIGATEUR_PRINCIPAL', 'FINESS', 'SERVICE', 'DATE_DEBUT', 'DATE_FIN'];
				break;

			case self::TYPE_REPRISE_PATIENT_VARIABLE:

				$headersMandatory = ['ACRONYME', 'VARIABLE_TYPE', 'POSITION', 'LABEL_VARIABLE', 'IDENTIFICATION_ECRF', 'VISIBLE_LIST_PATIENT'];
				break;

			case self::TYPE_REPRISE_PATIENT_VISIT:

				$headersMandatory = ['ACRONYME', 'NOM_COURT_VISIT', 'LABEL_VISIT', 'POSITION', 'ORDRE', 'DELAY', 'PHASE', 'PRICE'];
				break;

			case self::TYPE_REPRISE_CENTER:

				$headersMandatory = ['ACRONYME', 'NUMERO', 'NOM'];
				break;
		}

		return $headersMandatory;
	}
}
