<?php

namespace App\ETMF\Service\ListGen;

use Symfony\Contracts\Translation\TranslatorInterface;

class FilterString extends AbstractFilterType
{
    /**
     * @var bool
     */
    private $like = true;

    /**
     * FilterSelect constructor.
     */
    public function __construct(array $settings)
    {
        $this->input = 'string';
        parent::__construct($settings);
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        parent::configureOptions();

        $this->resolver->setDefaults([
            'like' => true,
            'regex' => '',
            'errormsg' => '',
        ]);
    }

    public function setDefaultProp(): void
    {
        parent::setDefaultProp();
        $this->setLike($this->settings['like']);
    }

    public function getLike(): bool
    {
        return $this->like;
    }

    /**
     * @return $this
     */
    public function setLike(bool $like): self
    {
        $this->like = $like;

        return $this;
    }

    /**
     * array.
     */
    public function getParamValues(): array
    {
        if ($this->like) {
            $arr = [];
            foreach ($this->values as $value) {
                $arr[] = '%'.$value.'%';
            }

            return $arr;
        }

        return $this->values;
    }

    protected function getOP(): string
    {
        if ($this->like) {
            return 'LIKE';
        }

        return '=';
    }

    public function getWhereQuery(): array
    {
        return [[
            'field' => $this->field,
            'op' => $this->getOP(),
            'values' => $this->getParamValues(),
        ]];
    }

    public function render(string $label, ?TranslatorInterface $translator): string
    {
        $html = $this->htmlRenderPrefix;
        $html .= $this->renderHtmlElement('input', [
            'id' => $this->name,
            'name' => $this->name,
            'type' => 'text',
            'value' => (0 == count($this->values) ? '' : $this->values[0]),
            'placeholder' => $label,
            'autocomplete' => 'off',
        ]);
        $html .= $this->htmlRenderSuffix;

        return $html;
    }
}
