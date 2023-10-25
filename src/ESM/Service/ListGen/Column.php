<?php

namespace App\ESM\Service\ListGen;

use App\ESM\Service\Utils\TranslatableTrait;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Options;

class Column extends AbstractColumnType
{
    use TranslatableTrait;

    /**
     * @var string
     */
    protected $label;
    /**
     * @var \Closure|string
     */
    protected $class;
    /**
     * @var bool
     */
    protected $sortable;
    /**
     * @var string
     */
    protected $sortField;
    /**
     * @var \Closure|string
     */
    protected $formatter;

    /**
     * @var \Closure|string
     */
    protected $formatterCsv;

    /**
     * Column constructor.
     */
    public function __construct(array $settings)
    {
        $this->type = 'normal';
        parent::__construct($settings);
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        parent::configureOptions();
        $this->resolver->setDefaults([
            'class' => '',
            'sortable' => true,
            'formatter' => null,
            'formatter_csv' => null,
            'translation_domain' => 'messages',
            'translation_args' => [],
            'sortField' => function (Options $options) {
                if ($options['sortable']) {
                    return $options['field'];
                }

                return '';
            },
        ]);
        $this->resolver->setRequired(['label']);
    }

    public function setDefaultProp(): void
    {
        parent::setDefaultProp();

        if (true == $this->settings['sortable'] && '' == $this->settings['sortField']) {
            throw new OptionDefinitionException("Error in ListGen Column settings => 'sortField' cannot be empty if 'sortable' is set to true.");
        }

        $this->setLabel($this->settings['label']);
        $this->setClass($this->settings['class']);
        $this->setSortable($this->settings['sortable']);
        $this->setFormatter($this->settings['formatter']);
        $this->setFormatterCsv($this->settings['formatter_csv']);
        $this->setSortField($this->settings['sortField']);
        $this->setTranslationArgs($this->settings['translation_args']);
        $this->setTranslationDomain($this->settings['translation_domain']);
        $this->setHidden(false);
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;

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

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function setSortField(string $sortField): self
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @param \Closure|string $class
     *
     * @return $this
     */
    public function setClass($class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getClass(array $row): string
    {
        return $this->stringOrClosure($this->class, [$row]);
    }

    /**
     * @param \Closure|string $formatter
     */
    public function setFormatter($formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @param \Closure|string $formatter
     */
    public function setFormatterCsv($formatter): self
    {
        $this->formatterCsv = $formatter;

        return $this;
    }

    public function render(array $row, bool $hasCheckBox = false): string
    {
        $html = $this->renderHtmlElement('td', [
            'class' => $this->getClass($row),
        ]);
        if ($hasCheckBox) {
            $html .= '<input autocomplete="off" type="checkbox" /> ';
        }

        return $html.$this->stringOrClosure($this->formatter, [$row], function ($formatter, $row) {
            return $row[$this->getFieldOrAlias()] ?? '';
        }).'</td>';
    }

    public function renderCsv(array $row): string
    {
        if (null === $this->formatterCsv) {
            return $row[$this->getFieldOrAlias()];
        } elseif ('formatter' === $this->formatterCsv) {
            return $this->stringOrClosure($this->formatter, [$row], function ($formatter, $row) {
                return $row[$this->getFieldOrAlias()] ?? '';
            });
        } else {
            return $this->stringOrClosure($this->formatterCsv, [$row], function ($formatter, $row) {
                return $row[$this->getFieldOrAlias()] ?? '';
            });
        }
    }

    protected function getFieldOrAlias(): string
    {
        if (null == $this->getAlias() && null == $this->getField()) {
            dump($this);
        } else {
            return $this->getAlias() ?? $this->getField();
        }
    }
}
