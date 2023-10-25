<?php

namespace App\ETMF\FormHandler;

use App\ESM\Entity\Exam;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\VariableType;
use App\ESM\Form\ExamType;
use App\ETMF\Form\ArtefactType;
use Doctrine\ORM\EntityManagerInterface;

class ArtefactHandler extends AbstractFormHandler
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	protected function getFormType(): string
	{
		return ArtefactType::class;
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
