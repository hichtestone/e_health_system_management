<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DeviationSystemCorrection;
use App\ESM\Form\DeviationSystemCorrectionType;
use Doctrine\ORM\EntityManagerInterface;

class DeviationSystemCorrectionHandler extends AbstractFormHandler
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	protected function getFormType(): string
	{
		return DeviationSystemCorrectionType::class;
	}

	/**
	 * @param DeviationSystemCorrection $data
	 */
	protected function process($data): void
	{
		$this->entityManager->persist($data);
		$this->entityManager->flush();
	}
}
