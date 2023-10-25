<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Form\DeviationSystemReviewType;
use Doctrine\ORM\EntityManagerInterface;

class DeviationSystemReviewHandler extends AbstractFormHandler
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	protected function getFormType(): string
	{
		return DeviationSystemReviewType::class;
	}

	/**
	 * @param DeviationSystemReview $data
	 */
	protected function process($data): void
	{
		$this->entityManager->persist($data);
		$this->entityManager->flush();
	}
}
