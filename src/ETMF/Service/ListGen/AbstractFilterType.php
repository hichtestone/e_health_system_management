<?php

namespace App\ETMF\Service\ListGen;

use App\ETMF\Service\Utils\StringOrClosureTrait;
use App\ETMF\Service\Utils\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

// todo filter date
// todo filter int
// todo filter select single/multiple

abstract class AbstractFilterType
{
    use StringOrClosureTrait;
    use TranslatableTrait;

    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $label;
    /**
     * @var \Closure|string
     */
    protected $class;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $values = [];
    /**
     * @var string
     */
    protected $htmlRenderPrefix = '<div class="form-group col-lg-3">';
    /**
     * @var string
     */
    protected $htmlRenderSuffix = '</div>';
    /**
     * @var string
     */
    protected $input = 'string';
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
            'class' => '',
            'translation_domain' => 'messages',
            'translation_args' => [],
            'defaultValues' => [''],
        ]);
        $this->resolver->setRequired(['field', 'label', 'name']);
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);
        $this->setField($this->settings['field']);
        $this->setLabel($this->settings['label']);
        $this->setClass($this->settings['class']);
        $this->setName($this->settings['name']);
        $this->setTranslationArgs($this->settings['translation_args']);
        $this->setTranslationDomain($this->settings['translation_domain']);
        $this->setValues($this->settings['defaultValues']);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
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

    public function getInput(): string
    {
        return $this->input;
    }

    public function setInput(string $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param \Closure|string $class
     */
    public function setClass($class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    protected function getParamValues(): array
    {
        return $this->values;
    }

    protected function getOP(): string
    {
        return 'IN';
    }

    public function getWhereQuery(): array
    {
        return [[
            'field' => $this->field,
            'op' => $this->getOP(),
            'values' => $this->getParamValues(),
        ]];
    }

    abstract public function render(string $label, ?TranslatorInterface $translator): string;
}
