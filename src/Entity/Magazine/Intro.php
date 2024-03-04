<?php

namespace App\Entity\Magazine;

use App\Model\Media\Image;
use App\Entity\Media\ImageTrait;

class Intro
{
    use ImageTrait;

    public function fetchImages(): array
    {
        return array_map(fn ($path) => new Image($path), glob($_ENV['PUBLIC_DIR'] . '/assets/intro/*.jpg'));
    }
}
