<?php

namespace App\Entity\Traits;

use App\Entity\Magazine\Models;
use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

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

        foreach ($entries as $entry) {
            $layout = $entry[$field];
            $ext = pathinfo($layout['path'], \PATHINFO_EXTENSION);
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $this->addImageData($layout);
            }
            if (in_array($ext, ['mp4', 'mov', 'wmv', 'ogv'])) {
                $layout['mediaType'] = 'video';
                $layout['src'] = str_replace(['mov', 'wmv', 'm4v'], 'mp4', $this->cockpitPathToUrl($layout['path']));
                $layout['poster'] = str_replace(['mp4', 'm4v'], 'jpg', $layout['src']);
            }
            $layouts[] = $layout;
        }

        $this->data['page']['layouts'] = $layouts ?? [];
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
