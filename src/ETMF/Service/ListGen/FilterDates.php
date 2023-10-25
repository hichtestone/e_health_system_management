<?php

namespace App\ETMF\Service\ListGen;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * gère l'affichage des éléments range of dates
 * Class FilterDates.
 */
class FilterDates extends AbstractFilterType
{
    /**
     * FilterSelect constructor.
     */
    public function __construct(array $settings)
    {
        $this->input = 'dates';
        parent::__construct($settings);
        $this->configureOptions();
        $this->setDefaultProp();
    }

    public function configureOptions(): void
    {
        parent::configureOptions();
    }

    public function setDefaultProp(): void
    {
        parent::setDefaultProp();
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
            'class' => 'flatpickr-range',
            'autocomplete' => 'off',
        ]);
        $html .= '<i class="fa fa-calendar fp-icon"></i>';
        $html .= $this->htmlRenderSuffix;

        return $html;
    }

    public function getWhereQuery(): array
    {
        $r = [];
        if (!empty($this->values) && '' !== $this->values[0]) {
            $this->values = explode(' to ', $this->values[0]);
            $r[] = [
                'field' => $this->field,
                'op' => '>=',
                'values' => [$this->values[0].' 00:00:00'],
            ];
            if (count($this->values) > 1) {
                $i = 1;
            } else {
                $i = 0;
            }
            $r[] = [
                'field' => $this->field,
                'op' => '<=',
                'values' => [$this->values[$i].' 23:59:59'],
            ];
        }

        return $r;
    }
}
