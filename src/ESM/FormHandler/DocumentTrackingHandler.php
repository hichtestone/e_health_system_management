<?php

namespace App\ESM\FormHandler;

use App\ESM\Entity\DocumentTracking;
use App\ESM\Form\DocumentTrackingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class DocumentTrackingHandler extends AbstractFormHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFormType(): string
    {
        return DocumentTrackingType::class;
    }

    /**
     * @param $data
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param DocumentTracking $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            if (!$data->isToBeReceived() && !$data->isToBeSent()) {
                $this->form->get('toBeSent')->addError(new FormError('Au moins une des deux options doit être cochée'));

                return false;
            } else {
                $this->process($data);

                return true;
            }
        }

        return false;
    }
}
