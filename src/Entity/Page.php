<?php

namespace App\Entity;

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

    protected function addMenuItemsInfo(array $mainmenu): array
    {
        $itemInfo = function (array $item) {
            if (empty($item['url'])) {
                return [
                    'route' => 'none',
                    'section' => (string) s($item['title'])->slugify(),
                    'url' => '#',
                ];
            }
            return [
                'route' => $item['url'],
                'url' => substr_count($item['url'], 'http') ? $item['url'] : $this->router->generate($item['url']),
                'section' => (string) s($item['url'])->slugify(),
            ];
        };

        return array_map(function (array $item) use ($itemInfo) {
            return array_merge($item, $itemInfo($item));
        }, $mainmenu);
    }

    protected function getExtraData(): array
    {
        return [];
    }

    protected function fetchPageData(string $pagePath): ?array
    {
        return $this->fetchCockpitData(
            'collections:findOne',
            'pages',
            ['path' => $pagePath]
        );
    }

    protected function getDefaultPageData(string $pagePath): array
    {
        $social = $this->fetchCockpitData('collections:find', 'socialmenu');
        $this->addSocialIcons($social);

        $issues = $this->fetchCockpitData('collections:find', 'issues');
        $this->addUrlToIssues($issues);

        $mainmenu = $this->fetchCockpitData('collections:find', 'mainmenu');
        $mainmenu = $this->addMenuItemsInfo($mainmenu);

        return [
            'site' => [
                'logo' => $this->publicPath->getUrl('img/logo.svg'),
                'social' => $social,
                'issues' => $issues,
                'mainmenu' => $mainmenu,
                'icons' => [
                ],
            ],
            'page' => $this->fetchPageData($pagePath),
            'hasIntro' => false,
        ];
    }
}
