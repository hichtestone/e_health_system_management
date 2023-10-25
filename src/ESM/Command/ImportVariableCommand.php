<?php

namespace App\ESM\Command;

use App\ESM\Entity\VariableType;
use App\ESM\Service\Mailer\MailerService;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\ESM\Entity\Center;
use App\ESM\Entity\Patient;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ImportVariableCommand
 * @package App\Command
 */
class ImportVariableCommand extends Command
{
	private $em;

	private $importFileInteroperabilityPath;

	private $importFileInteroperabilityArchivePath;

	private $importFileInteroperabilityErrorPath;

	private $logger;

	private $mailer;

	private $notificationEmail;

	private $projects = [];

	private $centers = [];

	private $patients = [];

	private $patientVariable = [];

	private $errors = [];

	protected function configure(): void
	{
		$this
			->setName('import:variable')
			->setDescription('Importer les anciennes data du client dans ESM')
		;
	}

	/**
	 * ImportVariableCommand constructor.
	 * @param EntityManagerInterface $em
	 * @param ParameterBagInterface $parameters
	 * @param LoggerInterface $interoperabilityLogger
	 * @param MailerService $mailer
	 */
	public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters, LoggerInterface $interoperabilityLogger, MailerService $mailer)
	{
		parent::__construct();

		$this->em 									 = $em;
		$this->importFileInteroperabilityPath 		 = $parameters->get('IMPORTS_INTEROPERABILITY_PATH');
		$this->importFileInteroperabilityArchivePath = $parameters->get('IMPORTS_INTEROPERABILITY_ARCHIVE_PATH');
		$this->importFileInteroperabilityErrorPath 	 = $parameters->get('IMPORTS_INTEROPERABILITY_ERROR_PATH');
		$this->logger								 = $interoperabilityLogger;
		$this->mailer								 = $mailer;
		$this->notificationEmail					 = $parameters->get('email.notification.adress');
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
		$ioFiles->title(' ****** Start process IMPORT Variable ****** ');

		$processFiles = $this->getFiles();

		$ioFiles->progressStart(count($processFiles));

		foreach ($processFiles as $keyFile => $processFile) {

			$this->logger->info('Process file : ' . $processFile);

			if (!$variablesData = $this->extractData($processFile)) {
				continue;
			}

			$ioLines = new SymfonyStyle($input, $output);
			$ioLines->progressStart(count($variablesData));

			foreach ($variablesData as $keyLine => $variableData) {

				$row = $keyLine + 2;

				if (!$this->createPatientData($variableData, $processFile, $row)) {
					continue;
				}

				$ioLines->progressAdvance();
			}

			$ioLines->progressFinish();

			$this->logger->info('Process file : ' . $processFile . ' successful !');
			$this->moveToArchiveDirectory($processFile);

			$ioFiles->progressAdvance();
		}

		$ioFiles->progressFinish();

		if (count($this->errors) > 0) {
			$this->sendEmailNotifications();
			$ioFiles->success('import terminé avec erreurs (detail fichier log)');
		} else {
			$ioFiles->success('validation success');
		}

		return 0;
	}

	/**
	 * @return array
	 */
	private function getFiles(): array
	{
		$files = scandir($this->importFileInteroperabilityPath);

		unset($files[array_keys($files, ".")[0]]);
		unset($files[array_keys($files, "..")[0]]);
		unset($files[array_keys($files, "archive")[0]]);
		unset($files[array_keys($files, "error")[0]]);

		if (count($files) < 1) {
			exit;
		}

		return $files;
	}

	/**
	 * @param $filename
	 * @return array
	 */
	private function extractData($filename)
	{
		$headers = ['PROJECT_ACRONYM', 'CENTER_NUMBER', 'PATIENT_NUMBER', 'FIELD', 'VALUE'];
		$nbHeaders = count($headers);

		$fileHandle = fopen($this->importFileInteroperabilityPath . $filename, "r");
		$variablesData = [];

		$lineNumber = 1;
		while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {

			if ($lineNumber === 1) {

				$dataHeaders = explode(';', $row[0]);

				// test column number
				if (count($dataHeaders) !== $nbHeaders) {
					$this->logger->error('the number of column of this file : ' . $filename . ' does not match !');
					$this->errors[] = 'the number of column of this file : ' . $filename . ' does not match !';
					$this->moveToErrorDirectory($filename);
					return false;
				}

				// test column name
				$errorColumnName = false;
				for ($i=0 ; $i < $nbHeaders; $i++) {
					if ($dataHeaders[$i] !== $headers[$i]) {

						if ($dataHeaders[$i] === '') {
							$this->logger->error('the header column N°' . ($i+1) . ' is empty !');
							$this->errors[] = 'the header column N°' . ($i+1) . ' is empty !';
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

				$data = explode(';', $row[0]);
				$variableData = array_combine($headers, $data);
				$errorColumnEmpty = false;

				foreach ($variableData as $key => $value) {

					// test empty column
					if ($value === '' || $value == '' || $value === null) {
						$this->logger->error('the file ' . $filename . ' contains a empty value at row N°' . $lineNumber . ' column name : ' . $key);
						$this->errors[] = 'the file ' . $filename . ' contains a empty value at row N°' . $lineNumber . ' column name : ' . $key;
						$errorColumnEmpty = true;
					}
				}

				if ($errorColumnEmpty) {
					$this->moveToErrorDirectory($filename);
					return false;
				}

				$variablesData[] = $variableData;
			}

			$lineNumber++;
		}

		return $variablesData;
	}

	/**
	 * @param $variableData
	 * @param $processFile
	 * @param $row
	 * @return false
	 * @throws ORMException
	 */
	private function createPatientData($variableData, $processFile, $row): ?bool
	{
		$project 		 = $this->getProject($variableData['PROJECT_ACRONYM']);
		$center  		 = $this->getCenter($variableData['CENTER_NUMBER']);

		if (!$project || !$center) {
			return false;
		}

		$patient 		 = $this->getPatient($variableData['PATIENT_NUMBER'], $center, $project);
		$patientVariable = $this->getPatientVariable($variableData['FIELD']);

		if (!$patient || !$patientVariable) {
			return false;
		}

		try {

			$patientData = $this->getPatientData($patient, $patientVariable);
			$newPatientData = $patientData ?? new PatientData();

			$newPatientData->setPatient($patient);
			$newPatientData->setVariable($patientVariable);

			$this->setVariableData($newPatientData, $variableData['VALUE'], $patientVariable->getVariableType());

			if ($patientVariable->getPosition()) {
				$newPatientData->setOrdre($patientVariable->getPosition());
			} else {
				$newPatientData->setOrdre(10);
			}

			$this->em->persist($newPatientData);
			$this->em->flush();

		} catch (\Exception $exception) {

			$this->logger->error('The row N°' . $row . ' of the file : ' . $processFile . ' has any errors');
			$this->errors[] = 'The row N°' . $row . ' of the file : ' . $processFile . ' has any errors';

			$this->logger->error(
				'error persist PatientData entity into project :' . $project->getAcronyme() .
				' and center : ' 			. $center->getName() .
				' and patient : ' 			. $patient->getNumber() .
				' for patientVariable : ' 	. $patientVariable->getLabel() .
				' detail : ' 				. $exception->getMessage()
			);

			$this->errors[] =
				'error persist PatientData entity into ' 					. "<br>"  .
				' project :' 				. $project->getAcronyme() 		. "<br>"  .
				' and center : ' 			. $center->getName() 			. "<br>"  .
				' and patient : ' 			. $patient->getNumber() 		. "<br>"  .
				' for patientVariable : ' 	. $patientVariable->getLabel() 	. "<br>"  .
				' detail : ' 				. $exception->getMessage()
			;

			return null;
		}

		return true;
	}

	/**
	 * @param $acronym
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getProject($acronym): ?Project
	{
		if (!array_key_exists($acronym, $this->projects)) {
			$currentProject = $this->em->getRepository(Project::class)->findOneBy(['acronyme' => $acronym]);

			if (!$currentProject) {
				$this->logger->error('the project with acronym : ' . $acronym . ' does not exist');
				$this->errors[] = 'the project with acronym : ' . $acronym . ' does not exist';
				return null;
			} else {
				$this->projects[$acronym] = $this->em->getReference(Project::class, $currentProject->getId());
			}

		} else {
			$currentProject = $this->projects[$acronym];
		}

		return $currentProject;
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
				$currentPatient = $this->patients[$patientNumber];
			}

		} else {
			$currentPatient = $this->patients[$patientNumber];
		}

		return $currentPatient;
	}


	/**
	 * @param Patient $patient
	 * @param PatientVariable $patientVariable
	 * @return mixed|object
	 */
	private function getPatientData(Patient $patient, PatientVariable $patientVariable): ?PatientData
	{
		$currentPatientData = $this->em->getRepository(PatientData::class)->findOneBy([
			'variable' => $patientVariable,
			'patient'  => $patient
		]);

		return $currentPatientData ?? null;
	}

	/**
	 * @param $sourceField
	 * @return mixed|object
	 * @throws ORMException
	 */
	private function getPatientVariable($sourceField): ?PatientVariable
	{
		if (!array_key_exists($sourceField, $this->patientVariable)) {
			$currentPatientVariable = $this->em->getRepository(PatientVariable::class)->findOneBy(['sourceId' => $sourceField]);

			if (!$currentPatientVariable) {
				$this->logger->error('this Patient Variable with identifiant eCRF : ' . $sourceField . ' does not exist');
				$this->errors[] = 'this Patient Variable with identifiant eCRF : ' . $sourceField . ' does not exist';
				return null;
			} else {
				$this->patientVariable[$sourceField] = $this->em->getReference(PatientVariable::class, $currentPatientVariable->getId());
			}

		} else {
			$currentPatientVariable = $this->patientVariable[$sourceField];
		}

		return $currentPatientVariable;
	}

	/**
	 * @param $filename
	 */
	private function moveToArchiveDirectory($filename): void
	{
		$originFilePath 	 = $this->importFileInteroperabilityPath . $filename;
		$destinationFilePath = $this->importFileInteroperabilityArchivePath . $filename;

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
		$originFilePath 	 = $this->importFileInteroperabilityPath . $filename;
		$destinationFilePath = $this->importFileInteroperabilityErrorPath . $filename;

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

		$this->mailer->sendEmail('notificationErrorInteroperability', [
			'errors' => $this->errors
		], 'ESM Unicancer - erreur import interoperabilité', [], [$this->notificationEmail]);

		$this->mailer->resetLocale();
	}

	/**
	 * @param PatientData $patientData
	 * @param $value
	 * @param $type
	 * @throws \Exception
	 */
	private function setVariableData(PatientData $patientData, $value, $type): void
	{
		if ($type->getLabel() === VariableType::TYPE_DATE_LABEL) {
			$this->isDateValue($value);
		}

		$patientData->setVariableValue($value);
	}

	/**
	 * @param $value
	 * @return void
	 * @throws Exception
	 */
	private function isDateValue($value): void
	{
		// main general test date format
		if (!is_string($value)) {
			throw new Exception('Le format de la date est incorrect : la valeur n\'est pas de type string.');
		} elseif (strlen($value) !== 10) {
			throw new Exception('Le format de la date est incorrect : le nombre de caractère doit être de 10.');
		} elseif (strpos($value, '-') === false) {
			throw new Exception('Le format de la date est incorrect : le caractère de séparation \'-\' est inexistant.');
		}

		// test ISO date format
		if (!is_numeric($value[0]) || !is_numeric($value[1]) || !is_numeric($value[2]) || !is_numeric($value[3])) {
			throw new Exception('Le format de la date est incorrect : les 4 premiers caractères ne sont pas numériques');
		} elseif ($value[4] !== '-' || $value[7] !== '-') {
			throw new Exception('Le format de la date est incorrect : les caractères de séparations \'-\' sont mal placés.');
		} elseif (!is_numeric($value[5]) || !is_numeric($value[6])) {
			throw new Exception('Le format de la date est incorrect : les caractères du mois ne sont numeriques.');
		} elseif (!is_numeric($value[8]) || !is_numeric($value[9])) {
			throw new Exception('Le format de la date est incorrect : les caractères du jour ne sont numeriques.');
		}

		// test day - month - year format
		$dateArray = explode('-', $value);
		[$year, $month, $day] = $dateArray;

		if (strlen($year) !== 4) {
			throw new Exception('Le format de la date est incorrect : annnée est exprimé avec le mauvais nombre de caractères.');
		} elseif ($year < 1900 || $year > 2300) {
			throw new Exception('Le format de la date est incorrect : annnée pas comprise entre 1900 et 2300.');
		}

		if (strlen($month) !== 2) {
			throw new Exception('Le format de la date est incorrect : mois est exprimé avec le mauvais nombre de caractères.');
		} elseif ($month < 1 || $month > 12) {
			throw new Exception('Le format de la date est incorrect : mois pas compris entre 1 et 12.');
		}

		if (strlen($day) !== 2) {
			throw new Exception('Le format de la date est incorrect : jour est exprimé avec le mauvais nombre de caractères.');
		} elseif ($day < 1 || $day > 31) {
			throw new Exception('Le format de la date est incorrect : jour pas compris entre 1 et 31.');
		}
	}
}
