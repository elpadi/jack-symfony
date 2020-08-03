<?php

namespace App\Entity;

use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

trait Shortcode
{
    use Cockpit;

    protected $shortcodeHandlers;

    /**
     * [layouts collection=gallorelayouts field=media]
     */
    public function layoutsShortcode(ShortcodeInterface $shortcode): string
    {
        $entries = $this->fetchCockpitData(
            'collections:find',
            $shortcode->getParameter('collection'),
        );
        $field = $shortcode->getParameter('field');

        foreach ($entries as $entry) {
            $layout = $entry[$field];
            $ext = pathinfo($layout['path'], \PATHINFO_EXTENSION);
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $this->addImageData($layout);
            }
            if (in_array($ext, ['mp4', 'mov', 'wmv', 'ogv'])) {
                $layout['mediaType'] = 'video';
                $layout['src'] = $this->cockpitPathToSymfonyPath($layout['path']);
            }
            $layouts[] = $layout;
        }

        $this->data['page']['layouts'] = $layouts ?? [];
        return "{% include 'partial/layouts.html.twig' with {'layouts': page.layouts} only %}";
    }

    protected function initShortcodes(): void
    {
        $this->shortcodeHandlers = new HandlerContainer();
        $this->shortcodeHandlers->add('layouts', [$this, 'layoutsShortcode']);
    }

    protected function parseShortcodes(string $content): string
    {
        $processor = new Processor(new RegularParser(), $this->shortcodeHandlers);
        return $processor->process($content);
    }
}
