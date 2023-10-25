<?php

namespace App\ESM\Service\ListGen;

use App\ESM\Service\Utils\StringOrClosureTrait;
use Closure;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class Action
 * @package App\ESM\Service\ListGen
 */
class Action
{
    use StringOrClosureTrait;

    /**
     * @var bool
     */
    private $ajax;

    /**
     * @var string
     */
    private $href;

    /**
     * @var string|Closure
     */
    private $title;

    /**
     * @var Closure|string
     */
    private $class;

    /**
     * @var Closure|string
     */
    private $formatter;

    /**
     * @var array
     */
    private $dataAttributes;

    /**
     * @var array
     */
    private $afterUpdEffect;

    /**
     * @var Closure
     */
    private $displayer;

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
            'ajax' => false,
            'title' => '',
            'class' => '',
            'formatter' => null,
            'data' => [],
            'afterUpdEffect' => [],
            'displayer' => null,
        ]);
        $this->resolver->setRequired(['href']);
    }

    protected function setDefaultProp(): void
    {
        $this->settings = $this->resolver->resolve($this->settings);
        $this->setHref($this->settings['href']);
        $this->setTitle($this->settings['title']);
        $this->setAjax($this->settings['ajax']);
        $this->setClass($this->settings['class']);
        $this->setFormatter($this->settings['formatter']);
        $this->setDataAttributes($this->settings['data']);
        $this->setAfterUpdEffect($this->settings['afterUpdEffect']);
        $this->setDisplayer($this->settings['displayer']);
    }

    /**
     * @param array $afertUpdEffect
     * @return $this
     */
    public function setAfterUpdEffect(array $afertUpdEffect): self
    {
        $this->afterUpdEffect = $afertUpdEffect;

        return $this;
    }

    /**
     * @return array
     */
    public function getAfterUpdEffect(): array
    {
        return $this->afterUpdEffect;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setDataAttributes(array $data): self
    {
        $this->dataAttributes = $data;

        return $this;
    }

    /**
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function addDataAttribute(string $key, string $val): self
    {
        $this->dataAttributes[$key] = $val;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataAttributes(): array
    {
        return $this->dataAttributes;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getDataAttribute(string $key): string
    {
        if (isset($this->dataAttributes[$key])) {
            return $this->dataAttributes[$key];
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->ajax;
    }

    /**
     * @param bool $ajax
     * @return $this
     */
    public function setAjax(bool $ajax): self
    {
        $this->ajax = $ajax;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    public function getHref(array $row): string
    {
        return $this->stringOrClosure($this->href, [$row]);
    }

    /**
     * @param string|Closure $href
     */
    public function setHref($href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|Closure $title
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param Closure|string $class
     */
    public function setClass($class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    public function getClass(array $row): string
    {
        return $this->stringOrClosure($this->class, [$row]);
    }

    /**
     * @param Closure|string $formatter
     */
    public function setFormatter($formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @param Closure|null $displayer
     * @return $this
     */
    public function setDisplayer(?Closure $displayer): self
    {
        $this->displayer = $displayer;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    public function render(array $row): string
    {
        if (null === $this->displayer || $this->stringOrClosure($this->displayer, [$row, $this->security])) {
            $arrAtttributes = [
                'class' => $this->getClass($row).($this->isAjax() ? ' lg-ajax' : ''),
                'href' => $this->getHref($row),
                'title' => $this->stringOrClosure($this->title, [$row]),
            ];
            foreach ($this->dataAttributes as $attr => $data) {
                $arrAtttributes['data-'.$attr] = $data;
            }
            $html = $this->renderHtmlElement('a', $arrAtttributes);

            return $html.$this->stringOrClosure($this->formatter, [$row]).'</a>';
        }

        return '';
    }
}
