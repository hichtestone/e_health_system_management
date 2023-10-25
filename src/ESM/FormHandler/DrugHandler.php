<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\Drug;
use App\ESM\Form\DrugType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class DrugHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DrugType::class;
    }

    /**
     * @param Drug $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param Drug $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $options = [];

        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $name = $this->entityManager->getRepository(Drug::class)->findOneBy(['name' => $data->getName()]);
            if (null !== $name && $name->getId() !== $data->getId()) {
                $this->form->get('name')->addError(new FormError('drug.name'));
                return false;
            }
            $this->process($data);

            return true;
        }

        return false;
    }
}
