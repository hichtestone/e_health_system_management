<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\PhaseSetting;
use App\ESM\Form\PhaseSettingType;
use Doctrine\ORM\EntityManagerInterface;

class PhaseSettingHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return PhaseSettingType::class;
    }

    /**
     * @param PhaseSetting $data
     */
    protected function process($data): void
    {
        if (null === $data->getOrdre()) {
            $data->setOrdre(0);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
