<?php

namespace App\Model\Media;

interface MediaInterface
{
    public function getMediaType(): string;
    public function getSrc(): string;
}
