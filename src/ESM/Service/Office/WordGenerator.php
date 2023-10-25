<?php

namespace App\ESM\Service\Office;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\ReportBlock;
use App\ESM\Entity\ReportBlockParam;
use App\ESM\Entity\ReportConfigVersion;
use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Entity\ReportVisit;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\DocProtect;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\VerticalJc;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WordGenerator
 * @package App\ESM\Service\Office
 */
class WordGenerator
{
	private $projectRootDir;
	private $documentsReportModelDir;
	private $documentsReportConfigDir;
	private $documentsReportVisitBeginDir;
	private $documentsTmpDir;
	private $imagesDir;
	private $dataTemplate;
	private $project;
	private $reportModel;
	private $translator;
	private $entityManager;

	/**
	 * WordGenerator constructor.
	 * @param string $projectRootDir
	 * @param string $documentsReportsDir
	 * @param string $documentsTmpDir
	 * @param string $documentsReportModelDir
	 * @param string $documentsReportConfigDir
	 * @param string $documentsReportVisitBegin
	 * @param TranslatorInterface $translator
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(string $projectRootDir, string $documentsReportsDir, string $documentsTmpDir, string $documentsReportModelDir, string $documentsReportConfigDir, string $documentsReportVisitBegin, TranslatorInterface $translator, EntityManagerInterface $entityManager)
	{
		$this->translator                   = $translator;
		$this->documentsTmpDir              = $documentsTmpDir;
		$this->projectRootDir               = $projectRootDir;
		$this->documentsReportModelDir      = $documentsReportModelDir;
		$this->documentsReportConfigDir     = $documentsReportConfigDir;
		$this->documentsReportVisitBeginDir = $documentsReportVisitBegin;
		$this->imagesDir                    = $this->projectRootDir . '/assets/images/';
		$this->entityManager                = $entityManager;
	}

	/**
	 * @param $version
	 * @param bool $isVisit
	 * @param ReportVisit|null $reportVisit
	 * @return string
	 * @throws Exception Used to generate a word document for ReportModelVersion (without data) or ReportConfigVersion (with data)
	 */
	public function generateVersion($version, bool $isVisit = false, ReportVisit $reportVisit = null): string
	{
		Settings::setTempDir($this->documentsTmpDir);
		$phpWord = new PhpWord();
		// Header H1, H2 and H3
		$phpWord->addTitleStyle(1, array('size' => 16), array('numStyle' => 'hNum', 'numLevel' => 0));
		$phpWord->addTitleStyle(2, array('size' => 14), array('numStyle' => 'hNum', 'numLevel' => 1));
		$phpWord->addTitleStyle(3, array('size' => 12), array('numStyle' => 'hNum', 'numLevel' => 2));

		$this->setPropertiesDocument($phpWord);

		$blocksVersions = $version->getVersionBlocks();
		$section        = $phpWord->addSection();
		$this->setHeader($version, $section);

		foreach ($blocksVersions as $blockVersion) {

			$block = $blockVersion->getBlock();

			if (($version instanceof ReportModelVersion) || ($version instanceof ReportConfigVersion && $blockVersion->isActive())) {

				$this->createDataMapBlock($block);

				if ($version instanceof ReportConfigVersion) {
					$this->setDataTemplate($block, $version, $reportVisit);
				}

				$this->createBlock($phpWord, $section, $block);
			}
		}

		$objWriter     = IOFactory::createWriter($phpWord);
		$directorySave = $this->selectDirectory($version, $isVisit);
		$filename      = $this->generateFilename($version);

		$objWriter->save($directorySave . $filename);

		return $filename;
	}

	/**
	 * @param PhpWord $phpWord
	 */
	private function setPropertiesDocument(PhpWord $phpWord): void
	{
		// meta doc info
		$properties = $phpWord->getDocInfo();
		$properties->setCreator('My name');
		$properties->setCompany('Unicancer');
		$properties->setTitle('Rapport monitoring 2');
		$properties->setDescription('My description');
		$properties->setCategory('My category');
		$properties->setLastModifiedBy('My name');
		$properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
		$properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
		$properties->setSubject('My subject');
		$properties->setKeywords('my, key, word');

		$documentProtection = $phpWord->getSettings()->getDocumentProtection();
		$documentProtection->setEditing(DocProtect::TRACKED_CHANGES);
	}

	/**
	 * @param PhpWord $phpWord
	 * @param Section $section
	 * @param ReportBlock $block
	 * @return void
	 */
	private function createBlock(PhpWord $phpWord, Section $section, ReportBlock $block): void
	{
		$data = $this->getData($block);

		$this->renderBlock($phpWord, $section, $block, $data);
	}

	/**
	 * @param $block
	 * @return null
	 */
	private function getData($block)
	{
		switch ($block->getName()) {

			case 'identification':

				$data = null;
				break;

			case 'participants':

				$data = null;
				break;

			case 'validation':
				$data = null;
				break;

			case 'date_of_visits':
				$data = null;
				break;

			case 'documents_discussed':
				$data = null;
				break;

			case 'follow_up':
				$data = null;
				break;

			case 'close_out':
				$data = null;
				break;

			case 'table_of_patients_notes_checked':
				$data = null;
				break;

			case 'patient_status':
				$data = null;
				break;

			case 'action_issues_log':
				$data = null;
				break;

			case 'deviations_log':
				$data = null;
				break;

			default:
				$data = null;
				break;
		}

		return $data;
	}

	/**
	 * @param ReportBlock $block
	 */
	private function createDataMapBlock(ReportBlock $block)
	{
		$data = [];
		switch ($block->getName()) {

			case 'identification':

				$data['typeReport']             = '';
				$data['typeVisit']              = '';
				$data['templateName']           = '';
				$data['templateVersion']        = '';
				$data['author']                 = '';
				$data['authorSociety']          = '';
				$data['acronym']                = '';
				$data['investigationalProduct'] = '';
				$data['centerNumber']           = '';
				$data['centerName']             = '';
				$data['principalInvestigator']  = '';
				$data['city']                   = '';
				$data['country']                = '';

				break;
		}

		$this->dataTemplate['identification'] = $data;
	}

	/**
	 * @param $block
	 * @param $version
	 * @param $reportVisit
	 */
	private function setDataTemplate($block, $version, $reportVisit): void
	{
		switch ($block->getName()) {

			case 'identification':

				if ($version instanceof ReportConfigVersion) {

					if (!$this->project) {
						$this->project = $version->getConfig()->getProject();
					}

					if (!$this->reportModel) {
						$this->reportModel = $version->getConfigModel();
					}

					$this->dataTemplate['identification']['typeReport']             = $this->translator->trans(ReportModel::REPORT_TYPE[$this->reportModel->getReportType()]);
					$this->dataTemplate['identification']['typeVisit']              = $this->translator->trans(ReportModel::VISIT_TYPE[$this->reportModel->getVisitType()]);
					$this->dataTemplate['identification']['templateName']           = $version->getModelName();
					$this->dataTemplate['identification']['templateVersion']        = $version->getModelVersion();
					$this->dataTemplate['identification']['author']                 = $reportVisit ? $reportVisit->getReportedBy() : '';
					$this->dataTemplate['identification']['authorSociety']          = $reportVisit ? utf8_decode($reportVisit->getReportedBy()->getSociety()) : '';
					$this->dataTemplate['identification']['acronym']                = $this->project->getAcronyme();
					$this->dataTemplate['identification']['investigationalProduct'] = $reportVisit ?  $this->getInvestigationalProduct($reportVisit) : '';
					$this->dataTemplate['identification']['centerNumber']           = $reportVisit ? $reportVisit->getCenter()->getNumber() : '';
					$this->dataTemplate['identification']['centerName']             = $reportVisit ? $reportVisit->getCenter()->getName() : '';
					$this->dataTemplate['identification']['principalInvestigator']  = $reportVisit ? $this->getPrincipalInvestigator($reportVisit) : '';
					$this->dataTemplate['identification']['city']                   = $reportVisit ? $this->getCity($reportVisit) : '';
					$this->dataTemplate['identification']['country']                = $reportVisit ? $this->getCountry($reportVisit) : '';

				} else {

					$this->dataTemplate['identification']['typeReport']             = '';
					$this->dataTemplate['identification']['typeVisit']              = '';
					$this->dataTemplate['identification']['templateName']           = '';
					$this->dataTemplate['identification']['templateVersion']        = '';
					$this->dataTemplate['identification']['author']                 = '';
					$this->dataTemplate['identification']['authorSociety']          = '';
					$this->dataTemplate['identification']['acronym']                = '';
					$this->dataTemplate['identification']['investigationalProduct'] = '';
					$this->dataTemplate['identification']['centerNumber']           = '';
					$this->dataTemplate['identification']['centerName']             = '';
					$this->dataTemplate['identification']['principalInvestigator']  = '';
					$this->dataTemplate['identification']['city']                   = '';
					$this->dataTemplate['identification']['country']                = '';
				}

				break;
		}
	}

	/**
	 * @param PhpWord $phpWord
	 * @param Section $section
	 * @param ReportBlock $block
	 * @param $data
	 * @return void
	 */
	private function renderBlock(PhpWord $phpWord, Section $section, ReportBlock $block, $data): void
	{
		$tableStyle = [
			'borderTopColor' => '0f0f0f',
			'borderTopSize' => 15,
			'borderBottomColor' => '0f0f0f',
			'borderBottomSize' => 15,
			'borderLeftColor' => '0f0f0f',
			'borderLeftSize' => 15,
			'borderRightColor' => '0f0f0f',
			'borderRightSize' => 15,
			'borderColor' => '0f0f0f'
		];
		$cellStyle  = [
			'borderTopColor' => '0f0f0f',
			'borderTopSize' => 15,
			'borderBottomColor' => '0f0f0f',
			'borderBottomSize' => 15,
			'borderLeftColor' => '0f0f0f',
			'borderLeftSize' => 15,
			'borderRightColor' => '0f0f0f',
			'borderRightSize' => 15,
			'borderColor' => '0f0f0f'
		];

		switch ($block->getName()) {

			case 'identification':

				$tableStyle = [
					'borderTopColor' => '000000',
					'borderTopSize' => 15,
					'borderBottomColor' => '000000',
					'borderBottomSize' => 15,
					'borderLeftColor' => '000000',
					'borderLeftSize' => 15,
					'borderRightColor' => '000000',
					'borderRightSize' => 15,
				];

				$section->addText('IDENTIFICATION', ['bold' => true]);

				$table = $section->addTable($tableStyle);

				$table->addRow();
				$cell1 = $table->addCell(5000);
				$cell1->addText(' Type of Report', ['bold' => true]);
				$cell2 = $table->addCell(5000);
				$cell2->addText($this->dataTemplate['identification']['typeReport']);
				$cell3 = $table->addCell(5000);
				$cell3->addText('Type of Visit', ['bold' => true]);
				$cell4 = $table->addCell(5000);
				$cell4->addText($this->dataTemplate['identification']['typeVisit']);

				$table->addRow();
				$cell5 = $table->addCell(5000);
				$cell5->addText(' Template Name', ['bold' => true]);
				$cell6 = $table->addCell(5000);
				$cell6->addText($this->dataTemplate['identification']['templateName']);
				$cell7 = $table->addCell(5000);
				$cell7->addText('Template Version', ['bold' => true]);
				$cell8 = $table->addCell(5000);
				$cell8->addText($this->dataTemplate['identification']['templateVersion']);

				$table->addRow();
				$cell9 = $table->addCell(5000, ['valign' => VerticalJc::CENTER]);
				$cell9->addText(' Author', ['bold' => true]);
				$cell10 = $table->addCell(5000);
				$cell10->addText($this->dataTemplate['identification']['author']);
				$cell11 = $table->addCell(5000);
				$cell11->addText('Author society', ['bold' => true]);
				$cell12 = $table->addCell(5000);
				$cell12->addText($this->dataTemplate['identification']['authorSociety']);

				$table->addRow();
				$cell13 = $table->addCell(5000, ['valign' => VerticalJc::CENTER]);
				$cell13->addText(' Acronym', ['bold' => true]);
				$cell14 = $table->addCell(5000);
				$cell14->addText($this->dataTemplate['identification']['acronym']);
				$cell15 = $table->addCell(5000);
				$cell15->addText('Investigational product', ['bold' => true]);
				$cell16 = $table->addCell(5000);
				$cell16->addText($this->dataTemplate['identification']['investigationalProduct']);

				$table->addRow();
				$cell17 = $table->addCell(5000, ['valign' => VerticalJc::CENTER]);
				$cell17->addText(' Center number', ['bold' => true]);
				$cell18 = $table->addCell(5000);
				$cell18->addText($this->dataTemplate['identification']['centerNumber']);
				$cell19 = $table->addCell(5000);
				$cell19->addText('Center name', ['bold' => true]);
				$cell20 = $table->addCell(5000);
				$cell20->addText($this->dataTemplate['identification']['centerName']);

				$table->addRow();
				$cell21 = $table->addCell(5000);
				$cell21->addText(' Principal investigator', ['bold' => true]);
				$cell22 = $table->addCell(5000);
				$cell22->addText($this->dataTemplate['identification']['principalInvestigator']);
				$cell23 = $table->addCell(5000);
				$cell23->addText('City', ['bold' => true]);
				$cell24 = $table->addCell(5000);
				$cell24->addText($this->dataTemplate['identification']['city']);

				$table->addRow();
				$cell25 = $table->addCell(5000);
				$cell25->addText(' Country', ['bold' => true]);
				$cell26 = $table->addCell(5000);
				$cell26->addText($this->dataTemplate['identification']['country']);
				$cell27 = $table->addCell(5000);
				$cell28 = $table->addCell(5000);

				break;

			case 'participants':

				$section->addText('PARTICIPANTS', ['bold' => true]);

				$table = $section->addTable($tableStyle);

				$table->addRow();
				$cell1 = $table->addCell(5000, $cellStyle);
				$cell1->addText('Organization', ['bold' => true]);
				$cell2 = $table->addCell(5000, $cellStyle);
				$cell2->addText('Name', ['bold' => true]);
				$cell3 = $table->addCell(5000, $cellStyle);
				$cell3->addText('Function', ['bold' => true]);

				$table->addRow();
				$cell4 = $table->addCell(5000, $cellStyle);
				$cell5 = $table->addCell(5000, $cellStyle);
				$cell6 = $table->addCell(5000, $cellStyle);

				$table->addRow();
				$cell7 = $table->addCell(5000, $cellStyle);
				$cell8 = $table->addCell(5000, $cellStyle);
				$cell9 = $table->addCell(5000, $cellStyle);

				break;

			case 'validation':

				$section->addText('VALIDATION', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell1 = $table1->addCell(5000);
				$cell1->addText('Author', ['bold' => true]);
				$cell11 = $table1->addCell(5000);
				$cell11->addText('');

				$table1->addRow();
				$cell1 = $table1->addCell(5000, $cellStyle);
				$cell1->addText('First name');
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table1->addRow();
				$cell1 = $table1->addCell(5000, $cellStyle);
				$cell1->addText('Surname');
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table1->addRow();
				$cell1 = $table1->addCell(5000, $cellStyle);
				$cell1->addText('Function');
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table1->addRow();
				$cell1 = $table1->addCell(5000, $cellStyle);
				$cell1->addText('Date');
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table1->addRow();
				$cell1 = $table1->addCell(5000, $cellStyle);
				$cell1->addText('Signature');
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');

				$section->addTextBreak();

				$table2 = $section->addTable($tableStyle);

				$table2->addRow();
				$cell1 = $table2->addCell(5000);
				$cell1->addText('Reviewer', ['bold' => true]);
				$cell11 = $table2->addCell(5000);
				$cell11->addText('');

				$table2->addRow();
				$cell1 = $table2->addCell(5000, $cellStyle);
				$cell1->addText('First name');
				$cell11 = $table2->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table2->addRow();
				$cell1 = $table2->addCell(5000, $cellStyle);
				$cell1->addText('Surname');
				$cell11 = $table2->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table2->addRow();
				$cell1 = $table2->addCell(5000, $cellStyle);
				$cell1->addText('Function');
				$cell11 = $table2->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table2->addRow();
				$cell1 = $table2->addCell(5000, $cellStyle);
				$cell1->addText('Date');
				$cell11 = $table2->addCell(5000, $cellStyle);
				$cell11->addText('');

				$table2->addRow();
				$cell1 = $table2->addCell(5000, $cellStyle);
				$cell1->addText('Signature');
				$cell11 = $table2->addCell(5000, $cellStyle);
				$cell11->addText('');

				break;

			case 'date_of_visits':

				$section->addText('DATE OF VISITS', ['bold' => true]);

				$section->addText('Were separated visits conducted ?      Oui      /      Non', ['bold' => true]);
				$section->addText('Tick On site Off site to specify the type of visit');

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Date of visit', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText(' Department name', ['bold' => true]);
				$cell13 = $table1->addCell(5000, $cellStyle);
				$cell13->addText(' On site', ['bold' => true]);
				$cell14 = $table1->addCell(5000, $cellStyle);
				$cell14->addText(' Of site', ['bold' => true]);

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText('');
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');
				$cell23 = $table1->addCell(5000, $cellStyle);
				$cell23->addText('');
				$cell24 = $table1->addCell(5000, $cellStyle);
				$cell24->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText('');
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');
				$cell33 = $table1->addCell(5000, $cellStyle);
				$cell33->addText('');
				$cell34 = $table1->addCell(5000, $cellStyle);
				$cell34->addText('');

				$table1->addRow();
				$cell41 = $table1->addCell(5000, $cellStyle);
				$cell41->addText('');
				$cell42 = $table1->addCell(5000, $cellStyle);
				$cell42->addText('');
				$cell43 = $table1->addCell(5000, $cellStyle);
				$cell43->addText('');
				$cell44 = $table1->addCell(5000, $cellStyle);
				$cell44->addText('');

				break;

			case 'documents_discussed':

				$section->addText('DOCUMENTS DISCUSSED', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Document', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText(' Version', ['bold' => true]);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('');
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText('');

				break;

			case 'follow_up':

				$section->addText('FOLLOW UP', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Date of initiation (Site)', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText('');

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText(' Date of initiation (Pharmacy)', ['bold' => true]);
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText(' Monitoring N°', ['bold' => true]);
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');

				$table1->addRow();
				$cell41 = $table1->addCell(5000, $cellStyle);
				$cell41->addText(' Date of monitoring', ['bold' => true]);
				$cell42 = $table1->addCell(5000, $cellStyle);
				$cell42->addText('');

				$table1->addRow();
				$cell51 = $table1->addCell(5000, $cellStyle);
				$cell51->addText(' Monitoring duration', ['bold' => true]);
				$cell52 = $table1->addCell(5000, $cellStyle);
				$cell52->addText('');

				$table1->addRow();
				$cell61 = $table1->addCell(5000, $cellStyle);
				$cell61->addText(' Date of last visit', ['bold' => true]);
				$cell62 = $table1->addCell(5000, $cellStyle);
				$cell62->addText('');

				break;

			case 'close_out':

				$section->addText('CLOSE OUT', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText('Date of initiation', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText('');

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText('Date of last visit', ['bold' => true]);
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText('Duration of last visit', ['bold' => true]);
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');

				$table1->addRow();
				$cell41 = $table1->addCell(5000, $cellStyle);
				$cell41->addText('Date of last pharmacy visit', ['bold' => true]);
				$cell42 = $table1->addCell(5000, $cellStyle);
				$cell42->addText('');

				$table1->addRow();
				$cell51 = $table1->addCell(5000, $cellStyle);
				$cell51->addText('Date of last pharmacy visit (On site / Off site', ['bold' => true]);
				$cell52 = $table1->addCell(5000, $cellStyle);
				$cell52->addText('');

				break;

			case 'table_of_patients_notes_checked':

				$section->addText('TABLE OF PATIENTS NOTES CHECKED', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Patient N°', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText(' ICF Date', ['bold' => true]);
				$cell13 = $table1->addCell(5000, $cellStyle);
				$cell13->addText(' Visits #', ['bold' => true]);
				$cell14 = $table1->addCell(5000, $cellStyle);
				$cell14->addText(' Verification (SDVed or consistencied) (page/item)', ['bold' => true]);
				$cell15 = $table1->addCell(5000, $cellStyle);
				$cell15->addText(' Collected / Monitored', ['bold' => true]);

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText('');
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');
				$cell23 = $table1->addCell(5000, $cellStyle);
				$cell23->addText('');
				$cell24 = $table1->addCell(5000, $cellStyle);
				$cell24->addText('');
				$cell25 = $table1->addCell(5000, $cellStyle);
				$cell25->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText('');
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');
				$cell33 = $table1->addCell(5000, $cellStyle);
				$cell33->addText('');
				$cell34 = $table1->addCell(5000, $cellStyle);
				$cell34->addText('');
				$cell35 = $table1->addCell(5000, $cellStyle);
				$cell35->addText('');

				$table1->addRow();
				$cell41 = $table1->addCell(5000, $cellStyle);
				$cell41->addText('');
				$cell42 = $table1->addCell(5000, $cellStyle);
				$cell42->addText('');
				$cell43 = $table1->addCell(5000, $cellStyle);
				$cell43->addText('');
				$cell44 = $table1->addCell(5000, $cellStyle);
				$cell44->addText('');
				$cell45 = $table1->addCell(5000, $cellStyle);
				$cell45->addText('');

				break;

			case 'patient_status':

				$section->addText('PATIENT STATUS', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Patient screened', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText('');

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText(' Patient included', ['bold' => true]);
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText(' Patient randomized', ['bold' => true]);
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');

				$table1->addRow();
				$cell41 = $table1->addCell(5000, $cellStyle);
				$cell41->addText(' Patient ongoing / on treatment', ['bold' => true]);
				$cell42 = $table1->addCell(5000, $cellStyle);
				$cell42->addText('');

				$table1->addRow();
				$cell51 = $table1->addCell(5000, $cellStyle);
				$cell51->addText(' Patient End of treatment', ['bold' => true]);
				$cell52 = $table1->addCell(5000, $cellStyle);
				$cell52->addText('');

				$table1->addRow();
				$cell51 = $table1->addCell(5000, $cellStyle);
				$cell51->addText(' Patient End of study', ['bold' => true]);
				$cell52 = $table1->addCell(5000, $cellStyle);
				$cell52->addText('');

				break;

			case 'action_issues_log':

				$section->addText('ACTION ISSUES LOG', ['bold' => true]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(5000, $cellStyle);
				$cell11->addText(' Notification Date', ['bold' => true]);
				$cell12 = $table1->addCell(5000, $cellStyle);
				$cell12->addText(' type', ['bold' => true]);
				$cell13 = $table1->addCell(5000, $cellStyle);
				$cell13->addText(' Status', ['bold' => true]);
				$cell14 = $table1->addCell(5000, $cellStyle);
				$cell14->addText(' Description', ['bold' => true]);
				$cell15 = $table1->addCell(5000, $cellStyle);
				$cell15->addText(' Action', ['bold' => true]);
				$cell16 = $table1->addCell(5000, $cellStyle);
				$cell16->addText(' Resolution Date', ['bold' => true]);

				$table1->addRow();
				$cell21 = $table1->addCell(5000, $cellStyle);
				$cell21->addText('');
				$cell22 = $table1->addCell(5000, $cellStyle);
				$cell22->addText('');
				$cell23 = $table1->addCell(5000, $cellStyle);
				$cell23->addText('');
				$cell24 = $table1->addCell(5000, $cellStyle);
				$cell24->addText('');
				$cell25 = $table1->addCell(5000, $cellStyle);
				$cell25->addText('');
				$cell26 = $table1->addCell(5000, $cellStyle);
				$cell26->addText('');

				$table1->addRow();
				$cell31 = $table1->addCell(5000, $cellStyle);
				$cell31->addText('');
				$cell32 = $table1->addCell(5000, $cellStyle);
				$cell32->addText('');
				$cell33 = $table1->addCell(5000, $cellStyle);
				$cell33->addText('');
				$cell34 = $table1->addCell(5000, $cellStyle);
				$cell34->addText('');
				$cell35 = $table1->addCell(5000, $cellStyle);
				$cell35->addText('');
				$cell36 = $table1->addCell(5000, $cellStyle);
				$cell36->addText('');

				break;

			case 'deviations_log':

				$section->addText('DEVIATION LOG', ['bold' => true]);

				$deviations = $this->entityManager->getRepository(Deviation::class)->findBy(['status' => Deviation::STATUS_IN_PROGRESS]);

				$table1 = $section->addTable($tableStyle);

				$table1->addRow();
				$cell11 = $table1->addCell(1700, $cellStyle);
				$cell11->addText(' Deviation code', ['bold' => true]);
				$cell12 = $table1->addCell(1700, $cellStyle);
				$cell12->addText(' Notification date', ['bold' => true]);
				$cell13 = $table1->addCell(1700, $cellStyle);
				$cell13->addText(' Category', ['bold' => true]);
				$cell14 = $table1->addCell(1700, $cellStyle);
				$cell14->addText(' Subcategory ', ['bold' => true]);
				$cell15 = $table1->addCell(1700, $cellStyle);
				$cell15->addText(' Description', ['bold' => true]);
				$cell16 = $table1->addCell(1700, $cellStyle);
				$cell16->addText(' Action', ['bold' => true]);
				$cell17 = $table1->addCell(1700, $cellStyle);
				$cell17->addText(' Resolution Date', ['bold' => true]);

				foreach ($deviations as $deviation) {
					$table1->addRow();

					$cell11 = $table1->addCell(1700, $cellStyle);
					$cell11->addText(' ' . $deviation->getCode(), ['bold' => false]);

					//Notification date : date de déclaration
					$cell12 = $table1->addCell(1700, $cellStyle);
					$cell12->addText($deviation->getDeclaredAt() ? $deviation->getDeclaredAt()->format('d-m-Y') : '', ['bold' => false]);

					//Category : Type de déviation
					$cell13 = $table1->addCell(1700, $cellStyle);
					$cell13->addText($deviation->getType(), ['bold' => false]);

					//Subcategory : Sous type de déviation
					$cell14 = $table1->addCell(1700, $cellStyle);
					$cell14->addText(' ' . $deviation->getSubType(), ['bold' => false]);

					//Description : Description
					$cell15 = $table1->addCell(1700, $cellStyle);
					$cell15->addText(' ' . $deviation->getDescription(), ['bold' => false]);

					//Date de résolution : laisser vide
					$cell16 = $table1->addCell(1700, $cellStyle);
					$cell16->addText('', ['bold' => false]);

					//Date de résolution : laisser vide
					$cell17 = $table1->addCell(1700, $cellStyle);
					$cell17->addText('', ['bold' => false]);
				}




				break;

			default:

				$section->addText(strtoupper($block->getName()), ['bold' => true]);
				$items = $this->entityManager->getRepository(ReportBlockParam::class)->findBy(['block' => $block], ['ordering' => 'ASC']);

				foreach ($items as $item) {
					$param = json_decode($item->getParam());
					switch ($param->input) {
						case 'text':
							$section->addText(ucwords($param->parameterName.':'));
							$table = $section->addTable($tableStyle);

							$table->addRow();
							$cell1 = $table->addCell(11904);
							$cell1->addText('');
							$cell1->addText('');
							$cell1->addText('');
							$cell1->addText('');
							$cell1->addText('');
							break;
						case 'table':
							if (count($items) > 1) {
								$section->addTitle(ucwords($param->parameterName), 3);
							}

							$table = $section->addTable($tableStyle);

							$table->addRow();
							$cellLength = (11904/$param->col);

							$table->addCell($cellLength, $cellStyle)->addText('', ['bold' => true]);
							for ($col = 0; $col < count($param->cells->cellCols); $col++) {
								$table->addCell($cellLength, $cellStyle)->addText(' ' . $param->cells->cellCols[$col], ['bold' => true]);
							}

							for ($row = 0; $row < count($param->cells->cellRows); $row++) {
								$table->addRow();
								$table->addCell($cellLength, $cellStyle)->addText(' ' . $param->cells->cellRows[$row], ['bold' => true]);
								for ($col = 0; $col < count($param->cells->cellCols); $col++) {
									$table->addCell($cellLength, $cellStyle)->addText('');
								}
							}
							break;
						case 'header':
							$section->addTitle(ucwords($param->label), substr($param->type, -1));
							break;
						case 'textarea':
							$section->addTitle(ucwords($param->parameterName), 3);
							$section->addText(ucwords($param->label));
							break;
						default :
							break;
					}
				}

				break;
		}

		$section->addTextBreak();
	}

	/**
	 * @param $version
	 * @return string
	 */
	private function generateFilename($version): string
	{
		if ($version instanceof ReportModelVersion) {

			$modelName    = $version->getReportModel()->getName();
			$modelVersion = $version->getNumberVersion();
			$filename     = 'rapportModel-' . $modelName . '-version-' . $modelVersion . '.docx';

		} elseif ($version instanceof ReportConfigVersion) {

			$modelName     = $version->getModelName();
			$modelVersion  = $version->getModelVersion();
			$configVersion = $version->getNumberVersion();
			$filename      = 'rapportConfig-' . $modelName . '-versionModel-' . $modelVersion . '-versionConfig-' . $configVersion . '.docx';
		}

		return $filename;
	}

	/**
	 * @param $version
	 * @param $section
	 */
	private function setHeader($version, $section): void
	{
		if ($version instanceof ReportModelVersion) {

			$versionName   = $version->getReportModel()->getName();
			$versionModel  = $version->getNumberVersion();
			$versionConfig = '';
			$headerText    = $versionName . ' (VersionModel : ' . $versionModel . ' )';

		} elseif ($version instanceof ReportConfigVersion) {

			$versionName   = $version->getModelName();
			$versionModel  = $version->getModelVersion();
			$versionConfig = $version->getNumberVersion();
			$headerText    = $versionName . ' (VersionModel : ' . $versionModel . ' VersionConfig : ' . $versionConfig . ')';
		}

		$header = $section->addHeader();

		$tableStyle = [
			'borderTopColor' => 'f39850',
			'borderTopSize' => 15,
			'borderBottomColor' => 'f39850',
			'borderBottomSize' => 15,
			'borderLeftColor' => 'f39850',
			'borderLeftSize' => 15,
			'borderRightColor' => 'f39850',
			'borderRightSize' => 15,
			'borderColor' => 'f39850',
		];

		$table = $header->addTable($tableStyle);
		$table->addRow(40);
		$table->addCell(1500, ['bgColor' => 'f39850', 'valign' => 'center'])->addImage($this->imagesDir . 'logo-unicancer.png', [
			'width' => 45,
			'height' => 45,
			'marginLeft' => 60,
			'marginTop' => 50,
			'marginBottom' => '50',
			'wrappingStyle' => 'square',
			'alignment' => Jc::CENTER
		]);

		$cellTitle = $table->addCell(10000, ['bgColor' => 'f39850', 'valign' => VerticalJc::CENTER, 'textAlignment' => 'center']);
		$cellTitle->addText($headerText, null, [
			'spaceBefore' => Converter::inchToTwip(0),
			'spaceAfter' => Converter::inchToTwip(0),
			'alignment' => Jc::CENTER]);

		$header->addTextBreak();
	}

	/**
	 * @param $version
	 * @param bool $isVisit
	 * @return string
	 */
	private function selectDirectory($version, bool $isVisit): string
	{
		if ($version instanceof ReportModelVersion) {

			$directory = $this->documentsReportModelDir;

		} elseif ($version instanceof ReportConfigVersion) {

			if ($isVisit) {
				$directory = $this->documentsReportVisitBeginDir;
			} else {
				$directory = $this->documentsReportConfigDir;
			}
		}

		return $directory;
	}

	/**
	 * @param $data
	 * @return array
	 */
	private function stringToArray($data)
	{
		if (is_object($data)) $data = get_object_vars($data);
		return is_array($data) ? array_map(__FUNCTION__, $data) : $data;
	}

	/**
	 * @param $reportVisit
	 * @return string
	 */
	private function getInvestigationalProduct($reportVisit)
	{
		return implode(',', array_unique(array_map(function ($trailTreatment) {
			if ($trailTreatment && 1 === $trailTreatment->getTrailTreatment()->getId()) {
				return $trailTreatment->getDrug() ? $trailTreatment->getDrug()->getName() : '';
			}}, $reportVisit->getProject()->getProjectTrailTreatments()->toArray()))
		);
	}

	/**
	 * @param $reportVisit
	 * @return string
	 */
	private function getCountry($reportVisit)
	{
		return implode(',', array_unique(array_map(function ($institution) {
			return $institution->getCountry()->getName();
			}, $reportVisit->getCenter()->getInstitutions()->toArray()))
		);
	}

	/**
	 * @param $reportVisit
	 * @return string
	 */
	private function getPrincipalInvestigator($reportVisit)
	{
		return implode(', ', $reportVisit->getCenter()->getPrincipalInvestigators()->toArray());
	}

	/**
	 * @param $reportVisit
	 * @return string
	 */
	private function getCity($reportVisit)
	{
		return implode(',', array_unique(array_map(function ($institution) {
			return $institution->getCity(); },
			$reportVisit->getCenter()->getInstitutions()->toArray()))
		);
	}
}
