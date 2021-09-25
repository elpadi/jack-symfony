<?php

namespace App\Entity;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait Cockpit
{
    protected static $dataCacheHours = 24;

    /**
     * Fetch data from cockpit.
     *
     * @param string $method {module}:{function} e.g. collections:findOne
     * @param array  $params parameters passed to cockpit function.
     *
     * @return array
     */
    protected function fetchCockpitData(string $method, ...$params): ?array
    {
        $cache = new FilesystemAdapter();
        $key = str_replace(
            ['{', '}', '/', ':'],
            ['[', ']', '-', ';'],
            $method . serialize($params)
        ) . $_ENV['COCKPIT_DATA_VERSION'];

        $fetch = function () use ($method, $params) {
            array_unshift($params, $method);
            return call_user_func_array('cockpit', $params);
        };

        if ($_SERVER['APP_DEBUG']) {
            return $fetch() ?: null;
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
