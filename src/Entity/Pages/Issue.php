<?php

namespace App\Entity\Pages;

use App\Entity\Cockpit\CockpitTrait;
use App\Entity\Media\ImageTrait;

class Issue extends Page
{
    use ImageTrait;
    use CockpitTrait;

    // @var array{title: string, slug: string, id: int, season: string, year: int, covers: array<array{path: string}>}
    protected $issue;
    // @var array<array{title: string, slug: string, image: array{path: string, title: string}}>
    protected array $layouts;

    public function fetchIssue(int $id, string $slug)
    {
        $this->issue = $this->fetchCockpitCollectionEntry('issues', compact('id'));
        $this->layouts = $this->fetchCockpitCollectionEntries(sprintf('issue%dlayouts', $id));

        if (empty($this->issue) || empty($this->layouts)) {
            throw new IssueNotFoundException("Issue $id/$slug not found");
        }
    }

    protected function addLayoutDimensions(&$layout): void
    {
        $reduceSize = function ($portraitSize, $landscapeSize) use ($layout) {
            return array_reduce(
                $layout['sheets'],
                /**
                 * @param array{value: array{orientation: 'portrait' | 'landscape', number: int, face: 'front' | 'back'}} $sheet
                 */
                function (int $size, array $sheet) use ($portraitSize, $landscapeSize) {
                    if ($sheet['value']['orientation'] === 'landscape') {
                        return $size + $landscapeSize;
                    }
                    if ($sheet['value']['orientation'] === 'portrait') {
                        return $size + $portraitSize;
                    }
                    return $size;
                },
                0
            );
        };
        $layout['width'] = $reduceSize(18, 24);
        $layout['height'] = $reduceSize(24, 18);
    }

    protected function preprocessData(&$data): void
    {
        foreach ($this->layouts as &$layout) {
            $size = $this->getImageDims($this->cockpitPathToRealPath($layout['image']['path']));
            $layout['image']['width'] = $size['width'];
            $layout['image']['height'] = $size['height'];

            if (isset($layout['sheets']) && is_array($layout['sheets']) && count($layout['sheets'])) {
                $this->addLayoutDimensions($layout);
            }
        }

        $data['page'] = [
            'title' => $this->issue['title'],
            'description' => 'Description of this issue.'
        ];

        $data['issue'] = $this->issue;
        $data['layouts'] = $this->layouts;
    }
}
