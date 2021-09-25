<?php

namespace App\Entity\Pages;

use App\Entity\Magazine\Intro;

class Home extends Page
{
    protected function getIntroData(): array
    {
        return [
            'showIntro' => true,
            'images' => (new Intro())->fetchImages(),
            'endRoute' => $_ENV['HOME_PAGE'] ?? 'home',
        ];
    }

    protected function preprocessData(&$data): void
    {
        $home = $this->fetchPageData($this->router->generate($_ENV['HOME_PAGE'] ?? 'home'));
        $data['page']['content'] = $home['content'];
        $data['hasIntro'] = !(isset($_COOKIE['has_seen_intro']) && $_COOKIE['has_seen_intro'] == '1');
        if ($data['hasIntro']) {
            $data['intro'] = $this->getIntroData();
        }
    }
}
