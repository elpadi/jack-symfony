<?php

namespace App\Model\Media;

use App\Entity\Media\ImageTrait;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use InvalidArgumentException;

class Image extends AbstractMedia
{
    use ImageTrait;

    private string $filePath;
    private int $width;
    private int $height;
    private array $srcsetByWidth = [];

    final public function getMediaType(): string
    {
        return 'image';
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->__get($name);
    }

    public function __get(string $key)
    {
        if (!isset($this->srcsetByWidth)) {
            $this->hydrate();
        }

        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        if ($key === 'srcset') {
            return $this->getSrcset($this->srcsetByWidth);
        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        if (isset($this->assetData[$key])) {
            return $this->assetData[$key];
        }

        return match ($key) {
            'title' => ucwords(basename($this->src, '.jpg')),
            'width' => 0,
            'height' => 0,
            'path' => str_replace('storage/', '', parse_url($this->src, \PHP_URL_PATH)),
        };

        throw new InvalidArgumentException("Unknow key {$key}.");
    }

    private function hydrate(): void
    {
        $this->filePath = $this->cockpitPathToRealPath($this->cockpitPath);

        $dims = empty($this->assetData['width']) ? $this->getImageDims($this->filePath) : $this->assetData;
        $this->width = (int) $dims->width;
        $this->height = (int) $dims->height;

        foreach ($this->getSrcsetSizesByWidth($this->width) as $width => $size) {
            $this->srcsetByWidth[$width] = $this->cockpitPathToUrl(
                dirname($this->cockpitPath) . "/sizes/{$size}/" . basename($this->cockpitPath)
            );
        }

        $this->src = current($this->srcsetByWidth);
    }
}
