<?php

namespace App\ESM\Service\ListGen;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * filtre basé sur une date nullable en select (oui/non/tous) (exemple: deleted_at, closed_at).
 */
class FilterArchived extends AbstractFilterType
{
    /**
     * FilterSelect constructor.
     */
    public function __construct(array $settings)
    {
        $this->input = 'archived';
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
        $html .= $this->renderHtmlElement('select', [
            'id' => $this->name,
            'name' => $this->name,
            'class' => 'form-control',
            'autocomplete' => 'off',
        ]);
        $value = count($this->values) > 0 ? $this->values[0] : '';
        $html .= '<option value=""'.('' == $value ? ' selected="selected"' : '').'><< '.$label.' >></option>';
        $html .= '<option value="0"'.('0' == $value ? ' selected="selected"' : '').'>'.$translator->trans('word.no').'</option>';
        $html .= '<option value="1"'.('1' == $value ? ' selected="selected"' : '').'>'.$translator->trans('word.yes').'</option>';
        $html .= '<option value="2"'.('2' == $value ? ' selected="selected"' : '').'>'.$translator->trans('word.all').'</option>';
        $html .= '</select>';
        $html .= $this->htmlRenderSuffix;

        return $html;
    }

    public function getWhereQuery(): array
    {
        if (count($this->values) > 0) {
            if ('0' == $this->values[0]) {
                return [[
                    'field' => $this->field,
                    'op' => 'IS',
                    'values' => ['NULL'],
                ]];
            } elseif ('1' == $this->values[0]) {
                return [[
                    'field' => $this->field,
                    'op' => 'IS NOT',
                    'values' => ['NULL'],
                ]];
            } else {
                // pas de filtre
                return [];
            }
        } else {
            // par défault on affiche pas les archivés
            return [[
                'field' => $this->field,
                'op' => 'IS',
                'values' => ['NULL'],
            ]];
        }
    }
}
