<?php
namespace App\Entity;

trait Page
{
    use Cockpit;

    protected $images;

    protected function addSocialIcons(&$social): void
    {
        foreach ($social as &$site) {
            $site['icon'] = $this->publicPath->getUrl('img/icons/' . $site['name'] . '.svg');
        }
    }

    protected function addUrlToIssues(&$issues): void
    {
        foreach ($issues as &$issue) {
            $issue['url'] = $this->router->generate('issue-layouts', $issue);
        }
    }

    protected function addUrlToMainMenu(&$menu): void
    {
        foreach ($menu as &$item) {
            $item['url'] = substr_count($item['url'], 'http') ? $item['url'] : $this->router->generate($item['url']);
        }
    }

    protected function getExtraData(): array
    {
        return [];
    }

    protected function fetchPageData(string $pagePath): array
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
        $this->addUrlToMainMenu($mainmenu);

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
        ];
    }
}
