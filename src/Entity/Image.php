<?php

namespace App\Entity;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait Image
{
    use Cockpit;

    protected function getImageSrcSetPart(string $path, int $width, string $size): string
    {
        return sprintf(
            '%s %d',
            $this->publicPath->getUrl("img/$size/$path"),
            $width
        ) . 'w';
    }

    protected function getImageSrcSet(string $path, int $width): string
    {
        return implode(
            ', ',
            [
                $this->getImageSrcSetPart($path, round($width / 2), 'half'),
                $this->getImageSrcSetPart($path, $width, 'original'),
            ]
        );
    }

    protected function getImageDims(string $path): array
    {
        $cache = new FilesystemAdapter();
        $imgFilePath = sprintf(
            '%s/img/original/%s',
            $_ENV['PUBLIC_DIR'],
            $path
        );

        if (!is_readable($imgFilePath)) {
            throw new InvalidArgumentException("Could not find image at $path.");
        }

        $key = str_replace('/', ';', $path) . filemtime($imgFilePath);

        $fetch = function () use ($imgFilePath) {
            return getimagesize($imgFilePath);
        };

        $parse = function($size) {
            return [
                'width' => $size[0],
                'height' => $size[1],
            ];
        };

        if (($_ENV['APP_ENV'] ?? 'prod') == 'dev') {
            return $parse($fetch());
        }

        $size = $cache->get(
            $key,
            function (ItemInterface $item) use ($fetch) {
                $item->expiresAfter(3600 * 24 * 7);
                return $fetch();
            }
        );

        return $parse($size);
    }

    protected function cockpitPathToSymfonyPath(string $path): string
    {
        return str_replace(['assets/', 'storage/'], '', $path);
    }

    protected function setImageDims(array &$img): void
    {
        foreach ($this->getImageDims($img['path']) as $dim => $size) {
            $img[$dim] = $size;
        }
    }

    protected function setImageSources(array &$img): void
    {
        $img['src'] = $this->publicPath->getUrl("img/quarter/$img[path]");
        $img['srcset'] = $this->getImageSrcSet($img['path'], $img['width']);
    }

    protected function addImageData(array &$img): void
    {
        $img['path'] = $this->cockpitPathToSymfonyPath($img['path']);
        $this->setImageDims($img);
        $this->setImageSources($img);
        $img['mediaType'] = 'image';
    }
}
