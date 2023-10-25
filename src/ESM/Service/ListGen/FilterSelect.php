<?php

namespace App\ESM\Service\ListGen;

use Closure;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterSelect extends AbstractFilterType
{
    /**
     * @var string|Closure
     */
    private $selectLabel;
    /**
     * @var array
     */
    private $optionsList;

    /**
     * FilterSelect constructor.
     */
    public function __construct(array $settings)
    {
        $this->input = 'select';
        parent::__construct($settings);
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        parent::configureOptions();
        $this->resolver->setDefaults([
            'selectLabel' => '',
        ]);
    }

    public function setDefaultProp(): void
    {
        parent::setDefaultProp();
        $this->setSelectLabel($this->settings['selectLabel']);
    }

    public function render(string $label, ?TranslatorInterface $translator): string
    {
        $html = $this->htmlRenderPrefix;
        //$html .= '<label>' . $this->getLabel() . '</label>';
        $html .= $this->renderSingleSelect($label);
        $html .= $this->htmlRenderSuffix;

        return $html;
    }

    private function renderSingleSelect(string $label): string
    {
        // on enlève les suffixes des noms de colonnes pour twig ou on garde les alias
        $arr = explode('.', $this->field);
        $this->field = end($arr);
        // html
        $html = $this->renderHtmlElement('select', [
            'id' => $this->name,
            'name' => $this->name,
            'class' => 'form-control',
            'autocomplete' => 'off',
        ]);
        $html .= '<option value=""><< '.$label.' >></option>';
        foreach ($this->optionsList as $option) {
            $selectLabel = $this->stringOrClosure($this->selectLabel, [$option[$this->field]], function ($d) use ($option) {
                if ('' == $d) {
                    return array_values($option)[0];
                }
                $arr = explode('.', $d);

                return $option[end($arr)] ?? '';
            });
            $html .= '<option value="'.$option[$this->field].'">'.$selectLabel.'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * @return string|Closure
     */
    public function getSelectLabel()
    {
        return $this->selectLabel;
    }

    /**
     * @param string|Closure $selectLabel
     *
     * @return $this
     */
    public function setSelectLabel($selectLabel): self
    {
        $this->selectLabel = $selectLabel;

        return $this;
    }

    /**
     * @param array $optionsList
     * @return FilterSelect
     */
    public function setOptionsList(array $optionsList): self
    {
        $this->optionsList = $optionsList;

        return $this;
    }
}
