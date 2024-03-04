<?php

namespace App\Entity\Pages;

use App\Entity\Cockpit\CockpitTrait;
use App\Entity\Magazine\Models;
use Thunder\Shortcode\{
    HandlerContainer\HandlerContainer,
    Parser\RegularParser,
    Processor\Processor,
    Shortcode\ShortcodeInterface,
};

use function Stringy\create as s;

trait ShortcodeTrait
{
    use CockpitTrait;

    protected $shortcodeHandlers;

    /**
     * [jbpc_models]
     */
    public function jbpcModelsShortcode(ShortcodeInterface $shortcode): string
    {
        $this->data['page']['models'] = (new Models())->fetchAll();
        return "{% include 'partial/models/list.html.twig' with {'models': page.models} only %}";
    }

    /**
     * [layouts collection=gallorelayouts field=media]
     */
    public function layoutsShortcode(ShortcodeInterface $shortcode): string
    {
        $entries = $this->fetchCockpitCollectionEntries($shortcode->getParameter('collection'));
        $field = $shortcode->getParameter('field');

        $this->data['page']['layouts'] = $this->parseAssets(array_map(fn ($entry) => $entry[$field], $entries ?: []));

        return "{% include 'partial/layouts.html.twig' with {'layouts': page.layouts} only %}";
    }

    protected function initShortcodes(): void
    {
        $this->shortcodeHandlers = new HandlerContainer();
        $shortcodes = [
            'layouts',
            'jbpc_models',
        ];
        foreach ($shortcodes as $shortcode) {
            $fn = (string) s($shortcode)->camelize() . 'Shortcode';
            $this->shortcodeHandlers->add($shortcode, [$this, $fn]);
        }
    }

    protected function parseShortcodes(string $content): string
    {
        $processor = new Processor(new RegularParser(), $this->shortcodeHandlers);
        return $processor->process($content);
    }
}
