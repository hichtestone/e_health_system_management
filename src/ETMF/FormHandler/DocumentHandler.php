<?php

namespace App\ETMF\FormHandler;

use App\ETMF\Entity\Document;
use App\ETMF\Form\DocumentVersionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DocumentHandler extends AbstractFormHandler
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	protected function getFormType(): string
	{
		return DocumentVersionType::class;
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
