<?php

namespace App\Entity\Magazine;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Entity\Images;

class Intro
{
    use Images;

    public function fetchImages(): array
    {
        $assetsDir = ($_ENV['PUBLIC_DIR'] ?? dirname(__DIR__, 3)) . '/assets';
        $paths = glob($assetsDir . '/intro/*.jpg');
        return array_map([$this, 'fetchImageInfo'], $paths);
    }
}
