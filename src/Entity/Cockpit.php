<?php

namespace App\Entity;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

trait Cockpit
{
    protected static $dataCacheHours = 24;

    protected function fetchCockpitCollectionEntries(string $collectionName): array
    {
        $entries = $this->fetchCockpitData("collections/entries/$collectionName");
        return $entries ? ($entries['entries'] ?? []) : [];
    }

    protected function fetchCockpitCollectionEntry(string $collectionName, array $filter): ?array
    {
        $entry = $this->fetchCockpitData(
            "collections/entry/$collectionName",
            compact('filter')
        );
        return $entry ?: null;
    }

    /**
     * Fetch data from cockpit using the API.
     *
     * @see https://getcockpit.com/documentation/api/collections
     *
     * @param string $apiPath e.g. collections/get/{collectionName}
     *
     * @return array
     */
    private function fetchCockpitData(string $apiPath, array $params = []): ?array
    {
        $cache = new FilesystemAdapter();
        $key = str_replace(
            ['{', '}', '/', ':'],
            ['[', ']', '-', ';'],
            $apiPath . serialize($params),
        ) . $_ENV['COCKPIT_DATA_VERSION'];

        $fetch = function () use ($apiPath, $params) {
            $apiParams = array_merge($params, [
                'token' => $_ENV['COCKPIT_API_TOKEN'],
            ]);
            $data = file_get_contents(
                sprintf(
                    '%s/%s?%s',
                    $_ENV['COCKPIT_API_URL'],
                    $apiPath,
                    http_build_query($apiParams)
                )
            );
            return $data ? json_decode($data, true) : null;
        };

        if ((int) $_ENV['COCKPIT_CACHE_ENABLED'] !== 1) {
            return $fetch() ?: null;
        }

        return $cache->get(
            $key,
            function (ItemInterface $item) use ($fetch) {
                $item->expiresAfter(3600 * static::$dataCacheHours);
                return $fetch();
            }
        );
    }
}
