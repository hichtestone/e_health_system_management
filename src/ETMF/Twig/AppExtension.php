<?php

namespace App\ETMF\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('gen_menu',
                [MenuExtension::class, 'gen_menu'],
                ['needs_environment' => true]
            ),
        ];
    }
}
