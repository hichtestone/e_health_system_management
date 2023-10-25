<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DeviationSystemActionType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DeviationSystemActionHandler
 * @package App\ESM\FormHandler
 */
class DeviationSystemActionHandler extends AbstractFormHandler
{
	private $entityManager;

	/**
	 * DeviationSystemActionHandler constructor.
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return string
	 */
	protected function getFormType(): string
	{
		return DeviationSystemActionType::class;
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
