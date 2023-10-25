<?php

namespace App\ETMF\Service\ListGen;

use App\ETMF\Service\Utils\StringOrClosureTrait;
use Closure;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ActionMulti
{
    use StringOrClosureTrait;

    /**
     * @var string
     */
    private $href;
    /**
     * @var string
     */
    private $label;
    /**
     * @var Closure
     */
    private $displayerRow;
    /**
     * @var Closure
     */
    private $displayer;
    /**
     * @var array
     */
    private $dataAttributes;

    /**
     * @var OptionsResolver
     */
    protected $resolver;
    /**
     * @var array
     */
    protected $settings;
    /**
     * @var Security
     */
    protected $security;
    /**
     * @var bool|null
     */
    private $display;

    /**
     * Action constructor.
     */
    public function __construct(array $settings, Security $security)
    {
        $this->security = $security;
        $this->settings = $settings;
        $this->resolver = new OptionsResolver();
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        $this->resolver->setDefaults([
            'href' => '',
            'label' => '',
            'displayerRow' => null,
            'data' => [],
            'displayer' => null,
        ]);
        $this->resolver->setRequired(['href', 'label']);
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);
        $this->setHref($this->settings['href']);
        $this->setLabel($this->settings['label']);
        $this->setDisplayerRow($this->settings['displayerRow']);
        $this->setDataAttributes($this->settings['data']);
        $this->setDisplayer($this->settings['displayer']);
    }

    public function setDataAttributes(array $data): self
    {
        $this->dataAttributes = $data;

        return $this;
    }

    public function addDataAttribute(string $key, string $val): self
    {
        $this->dataAttributes[$key] = $val;

        return $this;
    }

    public function getDataAttributes(): array
    {
        return $this->dataAttributes;
    }

    public function getDataAttribute(string $key): string
    {
        if (isset($this->dataAttributes[$key])) {
            return $this->dataAttributes[$key];
        }

        return '';
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): self
    {
        $this->href = $href;

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

    public function setDisplayerRow(?Closure $displayer): self
    {
        $this->displayerRow = $displayer;

        return $this;
    }

    public function setDisplayer(?Closure $displayer): self
    {
        $this->displayer = $displayer;

        return $this;
    }

    public function hasToBeDisplayed(): bool
    {
        if (null === $this->display) {
            if (null === $this->displayer || $this->stringOrClosure($this->displayer, [$this->security])) {
                $this->display = true;
            } else {
                $this->display = false;
            }
        }

        return $this->display;
    }

    public function renderMultiAction(): string
    {
        if ($this->hasToBeDisplayed()) {
            $arrAtttributes = [
                'value' => $this->href,
            ];
            foreach ($this->dataAttributes as $attr => $data) {
                $arrAtttributes['data-'.$attr] = $data;
            }
            $html = $this->renderHtmlElement('option', $arrAtttributes).$this->label.'</option>';

            return $html;
        }

        return '';
    }

    public function hasToRenderCheckBox(array $row): bool
    {
        if ($this->hasToBeDisplayed()) {
            if (null == $this->displayerRow || $this->stringOrClosure($this->displayerRow, [$row, $this->security])) {
                return true;
            }
        }

        return false;
    }
}
