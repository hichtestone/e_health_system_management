<?php

namespace App\ETMF\FormHandler;

use App\ETMF\Form\SectionType;
use Doctrine\ORM\EntityManagerInterface;

class SectionHandler extends AbstractFormHandler
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	protected function getFormType(): string
	{
		return SectionType::class;
	}

	/**
	 * @param $data
	 */
	protected function process($data): void
	{
		$this->entityManager->persist($data);
		$this->entityManager->flush();
	}
}
