<?php

namespace App\Entity\Pages;

use App\Entity\Cockpit\CockpitTrait;
use App\Entity\Media\ImageTrait;

use function Functional\pluck;

class Event extends Page
{
    use ImageTrait;
    use CockpitTrait;

    protected function getDeckImages(): array
    {
        $imgs = [];
        // @var array<array{title: string, image: array{path: string, title: string}}>
        $images = $this->fetchCockpitCollectionEntries('deck2016images');

        foreach (pluck($images, 'image') as $img) {
            $size = $this->getImageDims($this->cockpitPathToRealPath($img['path']));
            $img['width'] = $size['width'];
            $img['height'] = $size['height'];

            $imgs[] = $img;
        }

        return $imgs;
    }

    protected function getEventsData(): array
    {
        // @var array<array{title: string, description: string, date: string, address: string, map_url: string, start_date: string, end_date: string, street_address: string, locality: string, region: string, postal_code: string, formatted_start_date: string, formatted_end_date: string}>
        $events = $this->fetchCockpitCollectionEntries('events');
        foreach ($events as &$event) {
            $event['year'] = date('Y', strtotime($event['end_date']));
            if (substr_count($event['title'], 'Launch')) {
                $event['images'] = $this->getDeckImages();
            }
        }
        return $events;
    }

    protected function preprocessData(&$data): void
    {
        $data['events'] = $this->getEventsData();
    }
}
