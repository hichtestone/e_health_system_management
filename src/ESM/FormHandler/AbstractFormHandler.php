<?php

namespace App\ESM\FormHandler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormInterface
     */
    protected $form;

    abstract protected function getFormType(): string;

    /**
     * @param $data
     */
    abstract protected function process($data): void;

    /**
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param mixed $data
     */
    public function handle(Request $request, $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data);

            return true;
        }

        return false;
    }

    public function createView(): FormView
    {
        return $this->form->createView();
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
