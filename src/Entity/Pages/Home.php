<?php
namespace App\Entity\Pages;

use App\Entity\Magazine\Intro;

class Home extends Page
{
    protected function getIntroData(): array
    {
        if (isset($_COOKIE['has_seen_intro']) && $_COOKIE['has_seen_intro'] == '1') {
            return ['showIntro' => false];
        }
        return [
            'showIntro' => true,
            'images' => (new Intro())->fetchImages(),
            'endRoute' => 'jbpc',
        ];
    }

    protected function preprocessData(&$data): void
    {
        $jbpc = $this->fetchPageData('/jbpc');
        $data['page']['content'] = $jbpc['content'];

        $data['intro'] = $this->getIntroData();
    }
}
