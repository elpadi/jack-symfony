<?php

namespace App\Entity\Media;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait ImageTrait
{
    protected static $sizeCacheHours = 24 * 7;
    protected static $sizeCacheVersion = 1;

    /**
     * @return array{width: int, height: int}
     */
    protected function getImageDims(string $filePath): array
    {
        $cache = new FilesystemAdapter();
        $key = str_replace('/', ';', $filePath) . filemtime($filePath);

        $fetch = fn () => getimagesize($filePath);
        $parse = fn ($size) => ['width' => $size[0], 'height' => $size[1]];

        if ((bool) ($_ENV['IMG_RESIZE_CACHE_DISABLED'] ?? true)) {
            return $parse($fetch());
        }

        $size = $cache->get($key, function (ItemInterface $item) use ($fetch) {
            $item->expiresAfter(3600 * 24 * 7);
            return $fetch();
        });

        return $parse($size);
    }

    protected function getSrcsetSizesByWidth(int $width): array
    {
        return [
            round($width / 2) => 'half',
            $width => 'original',
        ];
    }

    protected function getSrcset(array $srcsetByWidth): string
    {
        $parts = array_map(
            fn ($src, $width) => "$src $width",
            array_values($srcsetByWidth),
            array_keys($srcsetByWidth)
        );

        return implode(', ', $parts);
    }
}
