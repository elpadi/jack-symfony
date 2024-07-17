<?php

namespace App\Model\NavMenu;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

use function Stringy\create as s;

class Item
{
    protected $title;
    protected $section;
    protected $url = '#';
    protected $route = 'none';
    protected $classNames = [];
    protected $rawItem;

    protected function __construct(string $title, string $route, string $url, array $rawItem)
    {
        $this->title = $title;
        $this->section = (string) s($title)->slugify();
        $this->route = $route;
        $this->url = $url;
        $this->rawItem = $rawItem;

        if (in_array($this->section, ['new'])) {
            $this->section = "page-{$this->section}";
        }
    }

    public static function createFromRawItem(array $rawItem, UrlGeneratorInterface $router): self
    {
        if (empty($rawItem['url'])) {
            return static::createFromUrl('#', $rawItem);
        }
        return static::createFromRoute($router, $rawItem['url'], $rawItem);
    }

    public static function createFromUrl(string $url, array $rawItem): self
    {
        return new Item(
            $rawItem['title'],
            'none',
            $url,
            $rawItem
        );
    }

    public static function createFromRoute(UrlGeneratorInterface $router, string $route, array $rawItem): self
    {
        try {
            $url = substr_count($route, 'http') ? $route : $router->generate($route);
        } catch (RouteNotFoundException $e) {
            $url = $router->generate('home') . $route;
        } catch (Throwable $e) {
            $url = '/';
        }
        return new Item(
            $rawItem['title'],
            $route,
            $url,
            $rawItem
        );
    }

    public function makeSelected(): void
    {
        $this->classNames[] = 'selected';
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
