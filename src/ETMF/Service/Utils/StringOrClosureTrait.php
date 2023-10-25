<?php

namespace App\ETMF\Service\Utils;

trait StringOrClosureTrait
{
    /**
     * @param string|\Closure|null $stringOrClosure
     * @param \Closure|null        $defaultClosure
     */
    public function stringOrClosure($stringOrClosure, array $args, $defaultClosure = null): string
    {
        if (!is_null($stringOrClosure)) {
            if (is_object($stringOrClosure) && $stringOrClosure instanceof \Closure) {
                return $stringOrClosure(...$args);
            } else {
                if (is_null($defaultClosure)) {
                    return $stringOrClosure;
                }
            }
        }
        if (is_object($defaultClosure) && $defaultClosure instanceof \Closure) {
            return $defaultClosure($stringOrClosure, ...$args);
        }

        return '';
    }

    protected function renderHtmlElement(string $element, array $arr): string
    {
        $r = [];
        foreach ($arr as $attr => $val) {
            if ('' != $val) {
                $r[] = $attr.'="'.$val.'"';
            }
        }

        return '<'.$element.' '.implode(' ', $r).(in_array($element, ['input', 'img']) ? ' />' : '>');
    }
}
