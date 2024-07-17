<?php

namespace App\Entity\Pages;

use App\Entity\Magazine\Intro;
use Throwable;

class Home extends Page
{
    protected function getIntroData(string $homePagePath): array
    {
        return [
            'showIntro' => true,
            'images' => (new Intro())->fetchImages(),
            'endRoute' => $homePagePath,
        ];
    }

    protected function preprocessData(&$data): void
    {
        try {
            $homePagePath = $this->router->generate('page', ['slug' => $_ENV['HOME_PAGE'] ?? 'home']);
        } catch (Throwable $error) {
            $homePagePath = '/home';
        }

        $home = $this->fetchPageData($homePagePath);
        $data['page']['content'] = $home['content'];
        $data['hasIntro'] = !(isset($_COOKIE['has_seen_intro']) && $_COOKIE['has_seen_intro'] == '1');
        if ($data['hasIntro']) {
            $data['intro'] = $this->getIntroData($homePagePath);
        }
    }
}
