<?php

namespace App\ETMF\Twig;

use App\ETMF\Twig\Tree\Treeview;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TreeviewExtension.
 */
class TreeviewExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('treeview',
                [Treeview::class, 'treeview']
            ),
            new TwigFunction('getJsTreeDataJson',
                [Treeview::class, 'getJsTreeDataJson']
            ),
        ];
    }
}
