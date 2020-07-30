<?php
namespace App\Entity;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait Images {

    protected static $sizeCacheHours = 24 * 7;
    protected static $sizeCacheVersion = 1;

    public function fetchImageInfo(string $path): array
    {
        $cache = new FilesystemAdapter();
        $assetsDir = dirname(__DIR__, 2) . '/assets';
        $urlPath = str_replace($assetsDir, '', $path);
        $cacheKey = sprintf('image-info-v%d-%s', static::$sizeCacheVersion, str_replace(['/','.'], '-', $path));

        return $cache->get($cacheKey , function (ItemInterface $item) use ($path, $urlPath) {
            $item->expiresAfter(3600 * static::$sizeCacheHours);
            return [
                'path' => $urlPath,
                'getimagesize' => getimagesize($path),
            ];
        });
    }

}
