<?php
namespace App\Entity;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait Cockpit {

    protected static $dataCacheHours = 24;

    protected function fetchCockpitData(string $method, ...$params): array
    {
        $cache = new FilesystemAdapter();
        $key = str_replace(['{', '}', '/', ':'], ['[', ']', '-', ';'], $method . serialize($params));
        $fetch = function () use ($method, $params) {
            array_unshift($params, $method);
            return call_user_func_array('cockpit', $params);
        };
        if (($_ENV['APP_ENV'] ?? 'prod') == 'dev') {
            return $fetch();
        }
        return $cache->get(
            $key,
            function (ItemInterface $item) use ($fetch) {
                $item->expiresAfter(3600 * static::$dataCacheHours);
                array_unshift($params, $method);
                if ($data = $fetch()) {
                    return $data;
                }
            }
        );
    }

}
