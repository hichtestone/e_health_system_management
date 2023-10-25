<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Service;
use App\ESM\Form\ServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Cocur\Slugify\Slugify;

class ServiceHandler extends AbstractFormHandler
{
    private $entityManager;
	private $slugify;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
		$this->slugify = new Slugify();
	}

    protected function getFormType(): string
    {
        return ServiceType::class;
    }

    /**
     * @param Service $data
     */
    protected function process($data): void
    {
        if ($data->isAddressInherited()) {
            $data->resetAddress();
        }

		$data->setSlug($this->slugify->slugify($data->getName()));

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
