<?php

namespace App\ESM\Service\ListGen;

class ColumnHidden extends AbstractColumnType
{
    /**
     * Column constructor.
     */
    public function __construct(array $settings)
    {
        $this->type = 'hidden';
        parent::__construct($settings);
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        parent::configureOptions();
        $this->resolver->setDefaults([
        ]);
    }

    public function setDefaultProp(): void
    {
        parent::setDefaultProp();
        $this->setHidden(true);
    }
}
