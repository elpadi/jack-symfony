<?php

namespace App\Entity\Pages;

class Issue extends Page
{
    protected $issue;
    protected $layouts;

    public function fetchIssue(int $id, string $slug)
    {
        $this->issue = $this->fetchCockpitCollectionEntry('issues', compact('id'));
        $this->layouts = $this->fetchCockpitCollectionEntries(sprintf('issue%dlayouts', $id));
    }

    protected function addLayoutDimensions(&$layout): void
    {
        $reduceSize = function ($portraitSize, $landscapeSize) use ($layout) {
            return array_reduce(
                $layout['sheets'],
                function ($size, $sheet) use ($portraitSize, $landscapeSize) {
                    extract($sheet['value']);
                    if ($orientation == 'landscape') {
                        return $size + $landscapeSize;
                    }
                    if ($orientation == 'portrait') {
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
            $this->addImageData($layout['image']);
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
