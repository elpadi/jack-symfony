<?php

namespace App\Entity\Cockpit;

use App\Model\Media\Factory as MediaFactory;
use App\Model\Media\MediaInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use InvalidArgumentException;

trait CockpitTrait
{
    protected static $defaultDataCacheTtlInHours = 2;

    protected function fetchCockpitCollectionEntries(string $collectionName): array
    {
        $entries = $this->fetchCockpitData("collections/entries/$collectionName");
        return $entries ? ($entries['entries'] ?? []) : [];
    }

    /**
     * @param array<int, array> $paths
     * @return MediaInterface[]
     */
    public function parseAssets(array $assets): array
    {
        return array_map(fn ($asset) => (new MediaFactory())->createFromPath($asset['path']), $assets);
    }

    public function cockpitPathToUrl(string $cockpitPath): string
    {
        if (strpos($cockpitPath, 'http') === 0) {
            return $cockpitPath;
        }

        $cockpitPath = '/' . trim($cockpitPath, '/');

        if (strpos($cockpitPath, '/assets/') === 0) {
            $cockpitPath = "/storage{$cockpitPath}";
        }

        return "https://cockpit.thejackmag.com{$cockpitPath}";
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

        if (($_COOKIE['nocache'] ?? '') === '1' || (int) $_ENV['COCKPIT_CACHE_ENABLED'] !== 1) {
            return $fetch() ?: null;
        }

        return $cache->get(
            $key,
            function (ItemInterface $item) use ($fetch) {
                $item->expiresAfter(3600 * intval($_ENV['DATA_CACHE_TTL_IN_HOURS'] ?? static::$defaultDataCacheTtlInHours));
                return $fetch();
            }
        );
    }

    /**
     * @throws InvalidArgumentException When the image at the given path is not found.
     */
    protected function cockpitPathToRealPath(string $path): string
    {
        if (strpos($path, $_ENV['COCKPIT_DIR']) === 0) {
            if (!is_readable($path)) {
                throw new InvalidArgumentException("Could not find image at $path.");
            }
            return realpath($path);
        }

        $paths = [
            // cockpit path
            sprintf(
                '%s/%s',
                $_ENV['COCKPIT_DIR'],
                $path
            ),
        ];

        foreach ($paths as $publicPath) {
            if (is_readable($publicPath)) {
                return realpath($publicPath);
            }
        }

        throw new InvalidArgumentException("Could not find image at $path.");
    }
}
