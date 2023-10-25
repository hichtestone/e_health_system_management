<?php

namespace App\ESM\Service\Deviation;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\DeviationSystem;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DeviationService
 * @package App\ESM\Service\Deviation
 */
class DeviationService
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * DeviceSession constructor.
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param Deviation $deviation
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviation(Deviation $deviation): array
	{
		$deviationGrade     = $deviation->getGrade();
		$isClosableMessages = null;
		$isClosable         = true;

		if ($deviationGrade === Deviation::GRADE_MAJEUR || $deviationGrade === Deviation::GRADE_CRITIQUE) {

			if (!$deviation->hasReview()) {
				$isClosable           = false;
				$isClosableMessages[] = 'La déviation ne possède pas de revues.';
			} else {
				if (!$deviation->hasOneLeastFinishedReview()) {
					$isClosable           = false;
					$isClosableMessages[] = 'La déviation ne possède pas au moins une revue terminée.';
				}
			}

			if (!$deviation->hasAction()) {
				$isClosable           = false;
				$isClosableMessages[] = 'La déviation ne possède pas d\'action.';
			} else {
				if ($deviation->hasLeastOneActionWithoutRealizedDate()) {
					$isClosable           = false;
					$isClosableMessages[] = 'Au moins une action de la déviation ne possède pas de date de réalisation.';
				}
			}

			if ($deviation->hasMeasureOfEfficiencyEmpty()) {
				$isClosable           = false;
				$isClosableMessages[] = 'La mesure de l\'efficacité de la déviation n\'est pas mentionnée.';
			}
		}

		if ($isClosable) {

			try {

				$deviation->setStatus(Deviation::STATUS_DONE);
				$deviation->setClosedAt(new \DateTime('now'));
				$this->em->persist($deviation);
				$this->em->flush();
				$isClosed = true;

			} catch (Exception $exception) {

				throw new Exception($exception->getMessage() . " - impossible to persist the closure deviation of" . $deviation->getCode());
			}

		} else {

			$isClosed = false;

		}

		return [
			'code' => $deviation->getCode(),
			'isClosed' => $isClosed,
			'isClosableMessages' => $isClosableMessages
		];
	}

	/**
	 * @param array $deviationsIDs
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviations(array $deviationsIDs): array
	{
		$res = [];
		foreach ($deviationsIDs as $deviationID) {

			$deviation = $this->em->getRepository(Deviation::class)->find($deviationID);

			if ($deviation->getStatus() === Deviation::STATUS_DRAFT || $deviation->getStatus() === Deviation::STATUS_DONE || $deviation->getClosedAt()) {
				continue;
			}

			$res[] = $this->closeDeviation($deviation);
		}

		return $res;
	}

	/**
	 * @param DeviationSample $deviationSample
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviationSample(DeviationSample $deviationSample): array
	{
		$deviationSampleGrade = $deviationSample->getGrade();
		$isClosableMessages   = null;
		$isClosable           = true;

		if ($deviationSampleGrade === Deviation::GRADE_MAJEUR || $deviationSampleGrade === Deviation::GRADE_CRITIQUE) {
			if (!$deviationSample->hasAction()) {
				$isClosable           = false;
				$isClosableMessages[] = 'La déviation échantillon biologique ne possède pas d\'action.';
			} else {
				if ($deviationSample->hasLeastOneActionWithoutRealizedDate()) {
					$isClosable           = false;
					$isClosableMessages[] = 'Au moins une action de la déviation échantillon biologique  ne possède pas de date de réalisation.';
				}
			}

			if ($deviationSample->hasMeasureOfEfficiencyEmpty()) {
				$isClosable           = false;
				$isClosableMessages[] = 'La mesure de l\'efficacité de la déviation échantillon biologique  n\'est pas mentionnée.';
			}
		}

		if ($isClosable) {
			try {
				$deviationSample->setStatus(Deviation::STATUS_DONE);
				$deviationSample->setClosedAt(new \DateTime('now'));
				$this->em->persist($deviationSample);
				$this->em->flush();
				$isClosed = true;

			} catch (\Exception $exception) {
				throw new Exception($exception->getMessage() . " - impossible to persist the closure deviation sample of" . $deviationSample->getCode());
			}

		} else {
			$isClosed = false;
		}

		return [
			'code' => $deviationSample->getCode(),
			'isClosed' => $isClosed,
			'isClosableMessages' => $isClosableMessages
		];
	}

	/**
	 * @param array $deviationSamplesIDs
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviationSamples(array $deviationSamplesIDs): array
	{
		$res = [];

		foreach ($deviationSamplesIDs as $deviationSampleID) {

			$deviationSample = $this->em->getRepository(DeviationSample::class)->find($deviationSampleID);

			if ($deviationSample->getStatus() === Deviation::STATUS_DRAFT || $deviationSample->getStatus() === Deviation::STATUS_DONE || $deviationSample->getClosedAt()) {
				continue;
			}

			$res[] = $this->closeDeviationSample($deviationSample);
		}

		return $res;
	}


	/**
	 * @param DeviationSystem $deviationSystem
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviationSystem(DeviationSystem $deviationSystem): array
	{
		$deviationSystemGrade = $deviationSystem->getGrade();
		$isClosableMessages   = null;
		$isClosable           = true;

		if (null === $deviationSystemGrade) {
			$isClosed = false;
			$isClosableMessages[] = 'Le grade n\'est pas rempli.';
		} else {
			if ($deviationSystemGrade === Deviation::GRADE_MAJEUR || $deviationSystemGrade === Deviation::GRADE_CRITIQUE) {

				if (!$deviationSystem->hasReview()) {
					$isClosable           = false;
					$isClosableMessages[] = 'La déviation ne possède pas de revues CREX.';
				} else {
					if (!$deviationSystem->hasOneLeastFinishedReview()) {
						$isClosable           = false;
						$isClosableMessages[] = 'La déviation ne possède pas au moins une revue CREX terminée.';
					}
				}

				if (!$deviationSystem->hasAction()) {
					$isClosable           = false;
					$isClosableMessages[] = 'La déviation ne possède pas d\'action.';
				} else {
					if ($deviationSystem->hasLeastOneActionWithoutRealizedDate()) {
						$isClosable           = false;
						$isClosableMessages[] = 'Au moins une action de la déviation ne possède pas de date de réalisation.';

					}
				}


				if ($deviationSystem->hasMeasureOfEfficiencyEmpty()) {
					$isClosable           = false;
					$isClosableMessages[] = 'La mesure de l\'efficacité de la déviation n\'est pas mentionnée.';
				}

				if ($deviationSystemGrade === Deviation::GRADE_CRITIQUE) {

					if ($deviationSystem->getVisaPilotProcessChiefQA() === null) {
						$isClosable           = false;
						$isClosableMessages[] = 'Le visa du pilote de processus AQ n\'est pas rempli.';
					}

					if ($deviationSystem->getVisaAt() === null) {
						$isClosable           = false;
						$isClosableMessages[] = 'La date de visa n\'est pas remplie.';
					}

					if ($deviationSystem->getOfficialQA() === null) {
						$isClosable           = false;
						$isClosableMessages[] = 'Le responsable AQ n\'est pas mentionné.';
					}
				}
			}

			if ($isClosable) {

				try {

					$deviationSystem->setStatus(Deviation::STATUS_DONE);
					$deviationSystem->setClosedAt(new \DateTime('now'));
					$this->em->persist($deviationSystem);
					$this->em->flush();
					$isClosed = true;

				} catch (Exception $exception) {

					throw new Exception($exception->getMessage() . " - impossible to persist the closure deviation of" . $deviationSystem->getCode());
				}

			} else {

				$isClosed = false;

			}
		}

		return [
			'code' => $deviationSystem->getCode(),
			'isClosed' => $isClosed,
			'isClosableMessages' => $isClosableMessages
		];
	}

	/**
	 * @param array $deviationSystemIDs
	 * @return array
	 * @throws Exception
	 */
	public function closeDeviationSystems(array $deviationSystemIDs): array
	{
		$res = [];

		foreach ($deviationSystemIDs as $deviationSystemID) {

			$deviationSystem = $this->em->getRepository(DeviationSystem::class)->find($deviationSystemID);

			if ($deviationSystem->getStatus() === Deviation::STATUS_DRAFT || $deviationSystem->getStatus() === Deviation::STATUS_DONE || $deviationSystem->getClosedAt()) {
				continue;
			}

			$res[] = $this->closeDeviationSystem($deviationSystem);
		}

		return $res;
	}
}
