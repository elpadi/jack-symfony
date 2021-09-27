<?php

namespace App\Entity\Magazine;

use App\Entity\Traits\ImageTrait;

class Models
{
    use ImageTrait;

    public function fetchAll(): array
    {
        $models = $this->fetchCockpitCollectionEntries('models');
        foreach ($models as &$model) {
            $this->addExtraInfo($model);
        }
        return $models;
    }

    private function addExtraInfo(&$model): void
    {
        foreach ($model['images'] as &$image) {
            $this->addImageData($image);
        }
    }
}
