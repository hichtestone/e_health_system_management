<?php

namespace App\ESM\FormHandler;

use App\ESM\Form\DeviationAndSampleType;
use Doctrine\ORM\EntityManagerInterface;

class DeviationAndSampleHandler extends AbstractFormHandler
{
	/**
	 * @var EntityManagerInterface
	 */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DeviationAndSampleType::class;
    }

    /**
     * @param mixed $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
