<?php

namespace App\Model\Media;

use App\Entity\Cockpit\CockpitTrait;

abstract class AbstractMedia implements MediaInterface
{
    use CockpitTrait;

    protected string $cockpitPath;
    protected array $assetData;
    protected string $src;

    final public function __construct(string $cockpitPath, array $assetData = [])
    {
        $this->cockpitPath = $cockpitPath;
        $this->assetData = $assetData;

        $this->src = $this->cockpitPathToUrl($cockpitPath);
    }

    abstract public function getMediaType(): string;

    public function getSrc(): string
    {
        return $this->src;
    }

    public function __get(string $key)
    {
        if ($key === 'mediaType') {
            return $this->getMediaType();
        }

        if ($key === 'src') {
            return $this->getSrc();
        }

        return null;
    }
}
