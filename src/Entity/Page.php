<?php

namespace App\Entity;

use App\Model\NavMenu\Item as NavMenuItem;

use function Functional\group;
use function Stringy\create as s;

trait Page
{
    use Cockpit;

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
        $mainmenu = $this->addMenuItemsInfo($mainmenu, $pagePath);

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
