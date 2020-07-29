<?php
namespace App\Entity\Pages;

class Event extends Page
{
    protected function getDeckImages(): array
    {
        $images = cockpit('collections:find', 'deck2016images');
        foreach ($images as &$img) {
            $path = str_replace('assets/', '', $img['image']['path']);
            $img['src'] = $this->publicPath->getUrl("img/quarter/$path");
            $img['srcset'] = $this->getImageSrcSet($path, (int) $img['width']);
        }
        return $images;
    }

    protected function getEventsData(): array
    {
        $events = $this->fetchCockpitData('collections:find', 'events');
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
