<?php

namespace App\Model\Media;

use InvalidArgumentException;

class Video extends AbstractMedia
{
    final public function getMediaType(): string
    {
        return 'video';
    }

    public function __get(string $key)
    {
        $value = parent::__get($key);
        if ($value !== null) {
            return $value;
        }

        if ($key === 'poster') {
            $ext = pathinfo($this->cockpitPath, \PATHINFO_EXTENSION);
            return str_replace(".$ext", '.jpg', $this->getSrc());
        }

        if (isset($this->assetData[$key])) {
            return $this->assetData[$key];
        }

        throw new InvalidArgumentException("Unknow key {$key}.");
    }
}
