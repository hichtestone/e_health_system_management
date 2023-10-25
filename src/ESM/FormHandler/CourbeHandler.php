<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\CourbeSetting;
use App\ESM\Entity\Point;
use App\ESM\Form\CourbeType;
use Doctrine\ORM\EntityManagerInterface;

class CourbeHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return CourbeType::class;
    }

    /**
     * @param CourbeSetting $data
     */
    protected function process($data): void
    {
        /** @var Point $points */
        $points = $this->entityManager->getRepository(Point::class)->findBy(['courbeSetting' => $data->getId()]);
        // Remove all points
        foreach ($points as $point) {
            $point->setCourbeSetting(null);
        }
        foreach ($data->getPoints() as $point) {
            $point->setCourbeSetting($data);
            //$data->addPoint($point);
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
