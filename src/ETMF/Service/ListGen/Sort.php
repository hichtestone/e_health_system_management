<?php

namespace App\ETMF\Service\ListGen;

use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Sort
{
    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $order;
    /**
     * @var int
     */
    protected $priority;

    /**
     * @var OptionsResolver
     */
    protected $resolver;
    /**
     * @var array
     */
    protected $settings;

    /**
     * Filter constructor.
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->resolver = new OptionsResolver();
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        $this->resolver->setDefaults([
            'order' => '',
            'priority' => 0,
        ]);
        $this->resolver->setRequired(['field']);
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);

        /*if($this->settings['field'] == ''){
            throw new OptionDefinitionException("Error in ListGen Sort settings => 'field' cannot be empty.");
        }*/

        $this->setField($this->settings['field']);
        $this->setOrder($this->settings['order']);
        $this->setPriority($this->settings['priority']);
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return $this
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return $this
     */
    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
