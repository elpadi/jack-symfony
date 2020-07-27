<?php
namespace App\Entity\Pages;

use App\Entity\Magazine\Intro;

class Event extends Page
{
    protected function getImageSrcSetPart(string $path, int $width, string $size): string
    {
        return sprintf(
            '%s %d',
            $this->publicPath->getUrl("img/$size/$path"),
            $width
        ).'w';
    }

    protected function getImageSrcSet(string $path, int $width): string
    {
        return implode(
            ', ',
            [
                $this->getImageSrcSetPart($path, round($width / 2), 'half'), 
                $this->getImageSrcSetPart($path, $width, 'original'), 
            ]
        );
    }

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
