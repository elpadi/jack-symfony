<?php

namespace App\Entity\Pages;

use App\Entity\{
    Shortcode,
    Cockpit,
    Image as ImageTrait,
    Page as PageTrait
};
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Page
{
    use Shortcode;
    use ImageTrait;
    use PageTrait;
    use Cockpit;

    protected $router;
    protected $data;

    public function __construct(KernelInterface $appKernel, UrlGeneratorInterface $router)
    {
        $this->appKernel = $appKernel;
        $this->router = $router;
        $this->publicPath = new PathPackage('', new EmptyVersionStrategy());
        $this->initShortcodes();
    }

    protected function preprocessData(&$data): void
    {
    }

    public function getPageData(string $pagePath = ''): array
    {
        $this->data = $this->getDefaultPageData(empty($pagePath) ? $_SERVER['REQUEST_URI'] : $pagePath);
        if (empty($this->data['page']['background'])) {
            unset($this->data['page']['background']);
        } else {
            $this->addImageData($this->data['page']['background']);
        }
        if (!empty($this->data['page']['content'])) {
            $this->data['page']['content'] = $this->parseShortcodes($this->data['page']['content']);
        }
        $this->preprocessData($this->data);
        return $this->data;
    }
}
