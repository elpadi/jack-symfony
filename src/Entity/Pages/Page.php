<?php

namespace App\Entity\Pages;

use App\Entity\Cockpit\CockpitTrait;
use App\Model\Media\Image;
use App\Model\NavMenu\Item as NavMenuItem;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

use function Functional\group;

class Page
{
    use CockpitTrait;
    use ShortcodeTrait;

    protected $router;
    protected $data;
    public $publicPath;

    public function __construct(UrlGeneratorInterface $router)
    {
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

    protected function addSocialIcons(&$social): void
    {
        foreach ($social as &$site) {
            $site['icon'] = $this->publicPath->getUrl('img/icons/' . $site['name'] . '.svg');
        }
    }

    protected function addUrlToIssues(&$issues): void
    {
        foreach ($issues as &$issue) {
            $issue['url'] = $this->router->generate(
                'issue-layouts',
                array_intersect_key(
                    $issue,
                    ['id' => '', 'slug' => '']
                )
            );
        }
    }

    protected function groupSubNavs(?array $mainsubnavitems): array
    {
        if (!$mainsubnavitems) {
            return [];
        }
        $groupedByParent = group($mainsubnavitems, function (array $item) {
            return $item['parent'];
        });
        foreach ($groupedByParent as $parent => $items) {
            $subNavs[] = compact('parent', 'items');
        }
        return $subNavs ?? [];
    }

    protected function addMenuItemsInfo(array $mainmenu): array
    {
        return array_map(function (array $item) {
            return NavMenuItem::createFromRawItem($item, $this->router)->toArray();
        }, $mainmenu);
    }

    protected function getExtraData(): array
    {
        return [];
    }

    protected function fetchPageData(string $pagePath): ?array
    {
        return $this->fetchCockpitCollectionEntry('pages', [
            'path' => $pagePath,
        ]);
    }

    protected function getDefaultPageData(string $pagePath): array
    {
        $social = $this->fetchCockpitCollectionEntries('socialmenu');
        $this->addSocialIcons($social);

        $issues = $this->fetchCockpitCollectionEntries('issues');
        $this->addUrlToIssues($issues);

        $mainmenu = $this->fetchCockpitCollectionEntries('mainmenu');
        $mainmenu = $this->addMenuItemsInfo($mainmenu);

        $mainsubnavs = $this->fetchCockpitCollectionEntries('mainsubnavs');
        $mainsubnavs = $this->groupSubNavs($mainsubnavs);

        return [
            'site' => [
                'logo' => $this->publicPath->getUrl('img/logo.svg'),
                'social' => $social,
                'issues' => $issues,
                'mainmenu' => $mainmenu,
                'mainsubnavs' => $mainsubnavs,
                'icons' => [
                ],
            ],
            'page' => $this->fetchPageData($pagePath),
            'hasIntro' => false,
        ];
    }
}
