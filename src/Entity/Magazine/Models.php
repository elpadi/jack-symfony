<?php

namespace App\Entity\Magazine;

use App\Entity\Cockpit\CockpitTrait;
use App\Entity\Media\ImageTrait;

class Models
{
    use CockpitTrait;
    use ImageTrait;

    public function fetchAll(): array
    {
        $models = $this->fetchCockpitCollectionEntries('models');

        foreach ($models as &$model) {
            $model['images'] = $this->parseAssets($model['images'] ?: []);
        }

        return $models;
    }
}
