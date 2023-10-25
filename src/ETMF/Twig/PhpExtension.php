<?php

namespace App\ETMF\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * extension pour ajouter des fonctions php qui n'existent pas par default dans twig
 * Class UtilsExtension.
 */
class PhpExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('repeat', [$this, 'repeat']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getenv', [$this, 'getenv']),
        ];
    }

    public function repeat(string $input, int $multiplier): string
    {
        return str_repeat($input, $multiplier);
    }

    /**
     * @return array|false|string
     */
    public function getenv(string $key)
    {
        return getenv($key);
    }
}
