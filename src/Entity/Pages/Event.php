<?php

namespace App\Entity\Pages;

use function Functional\pluck;

class Event extends Page
{
    protected function getDeckImages(): array
    {
        $images = $this->fetchCockpitCollectionEntries('deck2016images');
        foreach (pluck($images, 'image') as $img) {
            $this->addImageData($img);
            $imgs[] = $img;
        }
        return $imgs;
    }

    protected function getEventsData(): array
    {
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
