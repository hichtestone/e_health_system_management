<?php

namespace App\ESM\Twig;

use App\ESM\Twig\Tree\Treeview;
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
                [Treeview::class, 'treeview'],
            ),
            new TwigFunction(
                'tree',
                [Treeview::class, 'tree'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction('getJsTreeDataJson',
                [Treeview::class, 'getJsTreeDataJson']
            ),
        ];
    }
}
