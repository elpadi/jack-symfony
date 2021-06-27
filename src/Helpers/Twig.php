<?php

namespace App\Helpers;

use Twig\{
    Environment,
    TwigFilter
};

use function Stringy\create as s;

class Twig
{
    public static function init(Environment $twig): void
    {
        foreach ([
            'slugify'
        ] as $filter) {
            $twig->addFilter(new TwigFilter($filter, [__CLASS__, $filter]));
        }
    }

    public static function slugify(string $s): string
    {
        return (string) s($s)->slugify();
    }
}
