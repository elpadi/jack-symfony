<?php

namespace App\Entity\Pages;

use App\Entity\{
    Image as ImageTrait,
    Page as PageTrait
};
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Page
{
    use ImageTrait;
    use PageTrait;

    protected $router;

    public function __construct(KernelInterface $appKernel, UrlGeneratorInterface $router)
    {
        $this->appKernel = $appKernel;
        $this->router = $router;
        $this->publicPath = new PathPackage('', new EmptyVersionStrategy());
    }

    protected function preprocessData(&$data): void
    {
    }

    public function getPageData(string $pagePath = ''): array
    {
        $data = $this->getDefaultPageData(empty($pagePath) ? $_SERVER['REQUEST_URI'] : $pagePath);
        if (isset($data['page']['background'])) {
            $this->addImageData($data['page']['background']);
        }
        $this->preprocessData($data);
        return $data;
    }
}
