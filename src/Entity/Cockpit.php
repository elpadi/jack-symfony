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
		return $cache->get($key, function (ItemInterface $item) use ($method, $params) {
			$item->expiresAfter(3600 * static::$dataCacheHours);
			$methodParts = explode(':', $method);
			array_unshift($params, $method);
			if ($data = call_user_func_array('cockpit', $params)) {
				return $data;
			}
		});
	}

}
