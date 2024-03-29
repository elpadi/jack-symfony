<?php

namespace App\Entity\Pages;

use App\Entity\Cockpit\CockpitTrait;
use App\Model\Media\Image;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Page
{
    use PageTrait;
    use CockpitTrait;
    use ShortcodeTrait;

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
            $this->data['page']['background'] = new Image($this->data['page']['background']['path']);
        }

        $this->preprocessData($this->data);

        if (!empty($this->data['page']['content'])) {
            $this->data['page']['content'] = $this->parseShortcodes($this->data['page']['content']);
        }

        return $this->data;
    }
}
