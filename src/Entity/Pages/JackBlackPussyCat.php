<?php
namespace App\Entity\Pages;

use App\Entity\Page;
use App\Entity\Cockpit;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class JackBlackPussyCat
{
    use Page;

    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getPageData(): array
    {
        return array_merge_recursive(
            $this->getDefaultPageData(),
            [
                'page' => $this->fetchCockpitData(
                    'collections:findOne',
                    'pages',
                    ['path' => '/jbpc']
                ),
            ]
        );
    }
}
