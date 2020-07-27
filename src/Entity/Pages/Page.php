<?php
namespace App\Entity\Pages;

use App\Entity\Page as PageTrait;
use App\Entity\Cockpit;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Page
{
    use PageTrait;

    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->publicPath = new PathPackage('', new EmptyVersionStrategy());
    }

    protected function preprocessData(&$data): void
    {
    }

    public function getPageData(string $pagePath = ''): array
    {
        $data = $this->getDefaultPageData(empty($pagePath) ? $_SERVER['REQUEST_URI'] : $pagePath);
        $this->preprocessData($data);
        return $data;
    }
}
