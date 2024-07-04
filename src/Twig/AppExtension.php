<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\{
    TwigFunction,
    TwigFilter
};
use cebe\markdown\Markdown;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('md', function ($text) {
                return (new Markdown())->parse($text);
            }),
            new TwigFilter('mdp', function ($text) {
                return (new Markdown())->parseParagraph($text);
            }),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('region', function ($name) {
                return (new Markdown())->parse(file_get_contents(__DIR__ . "/../../regions/$name.md"));
            }),
        ];
    }
}
