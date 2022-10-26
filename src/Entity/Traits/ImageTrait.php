<?php

namespace App\Entity\Traits;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use InvalidArgumentException;

trait ImageTrait
{
    use CockpitTrait;

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

    /**
     * @throws InvalidArgumentException When the image at the given path is not found.
     */
    protected function findImageRealPath(string $path): string
    {
        if (strpos($path, $_ENV['PUBLIC_DIR']) === 0) {
            if (!is_readable($path)) {
                throw new InvalidArgumentException("Could not find image at $path.");
            }
            return realpath($path);
        }
        $paths = [
            // original size of resized images
            sprintf(
                '%s/img/original/%s',
                $_ENV['PUBLIC_DIR'],
                $path
            ),
            // assets path
            sprintf(
                '%s/assets/%s',
                $_ENV['PUBLIC_DIR'],
                $path
            ),
            // public path
            sprintf(
                '%s/%s',
                $_ENV['PUBLIC_DIR'],
                $path
            ),
        ];
        foreach ($paths as $publicPath) {
            if (is_readable($publicPath)) {
                return realpath($publicPath);
            }
        }
        throw new InvalidArgumentException("Could not find image at $path.");
    }

    protected function getImageDims(string $imgFilePath): array
    {
        $cache = new FilesystemAdapter();
        $key = str_replace('/', ';', $imgFilePath) . filemtime($imgFilePath);

        $fetch = function () use ($imgFilePath) {
            return getimagesize($imgFilePath);
        };

        $parse = function ($size) {
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

    protected function setImageDims(array &$img): void
    {
        foreach ($this->getImageDims($img['filePath']) as $dim => $size) {
            $img[$dim] = $size;
        }
    }

    protected function setImageSources(array &$img): void
    {
        if (strpos($img['filePath'], '/img/original/') === false) {
            $img['src'] = str_replace($_ENV['PUBLIC_DIR'], '', $img['filePath']);
            $img['srcset'] = '';
            return;
        }
        $img['src'] = $this->publicPath->getUrl("img/quarter/$img[path]");
        $img['srcset'] = $this->getImageSrcSet($img['path'], $img['width']);
    }

    protected function addImageData(array &$img): void
    {
        $img['filePath'] = $this->findImageRealPath($img['path']);
        $this->setImageDims($img);
        $this->setImageSources($img);
        $img['mediaType'] = 'image';
    }


    protected function fetchImageInfo(string $path): array
    {
        $img = compact('path');
        $this->addImageData($img);
        return $img;
    }
}
