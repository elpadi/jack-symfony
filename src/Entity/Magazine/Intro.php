<?php

namespace App\Entity\Magazine;

use App\Entity\Traits\ImageTrait;

class Intro
{
    use ImageTrait;

    public function fetchImages(): array
    {
        $paths = glob($_ENV['PUBLIC_DIR'] . '/assets/intro/*.jpg');
        return array_map([$this, 'fetchImageInfo'], $paths);
    }
}
