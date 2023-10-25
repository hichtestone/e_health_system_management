<?php

namespace App\ETMF\FormHandler;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface FormHandlerInterface.
 */
interface FormHandlerInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle(Request $request, $data, array $options = []): bool;

    public function createView(): FormView;
}
