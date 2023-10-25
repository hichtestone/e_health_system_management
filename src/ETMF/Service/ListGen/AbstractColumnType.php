<?php

namespace App\ETMF\Service\ListGen;

use App\ETMF\Service\Utils\StringOrClosureTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractColumnType
{
    use StringOrClosureTrait;

    /**
     * @var string sql field
     */
    protected $field;
    /**
     * @var string key in the result
     */
    protected $alias;
    /**
     * @var bool display or not the column
     */
    protected $hidden;
    /**
     * @var string
     */
    public $type;

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
    }

    public function configureOptions(): void
    {
        $this->resolver->setDefaults([
            'field' => '',
            'alias' => '',
        ]);
        $this->resolver->setRequired([]);
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);
        $this->field = $this->settings['field'];
        $this->alias = $this->settings['alias'];
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @return $this
     */
    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
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

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return $this
     */
    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
