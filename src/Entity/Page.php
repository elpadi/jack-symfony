<?php
namespace App\Entity;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

trait Page
{
    use Cockpit;

    protected $images;

    protected function addSocialIcons(&$social): void
    {
        foreach ($social as &$site) {
            $site['icon'] = $this->images->getUrl('icons/' . $site['name'] . '.svg');
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

    protected function getDefaultPageData(): array
    {
        $this->images = new PathPackage('/img', new EmptyVersionStrategy());

        $social = $this->fetchCockpitData('collections:find', 'socialmenu');
        $this->addSocialIcons($social);

        $issues = $this->fetchCockpitData('collections:find', 'issues');
        $this->addUrlToIssues($issues);

        $mainmenu = $this->fetchCockpitData('collections:find', 'mainmenu');
        $this->addUrlToMainMenu($mainmenu);

        return [
            'site' => [
                'logo' => $this->images->getUrl('logo.svg'),
                'social' => $social,
                'issues' => $issues,
                'mainmenu' => $mainmenu,
                'icons' => [
                ],
            ],
        ];
    }
}
