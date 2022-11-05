<?php

namespace App\Model\Media;

class Factory
{
    public function createFromPath(string $path): MediaInterface
    {
        $ext = pathinfo($path, \PATHINFO_EXTENSION);

        if ($this->isImage($ext)) {
            return new Image($path);
        }

        if ($this->isVideo($ext)) {
            return new Video($path);
        }
    }

    private function isImage(string $fileExtension): bool
    {
        return in_array($fileExtension, ['jpg', 'jpeg', 'png']);
    }

    private function isVideo(string $fileExtension): bool
    {
        return in_array($fileExtension, ['mp4', 'm4v', 'mov', 'wmv', 'ogv']);
    }
}
