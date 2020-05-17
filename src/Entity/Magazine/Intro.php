<?php
namespace App\Entity\Magazine;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Entity\Images;

class Intro {

	use Images;

	public function fetchImages() {
		$assetsDir = dirname(__DIR__, 3) . '/public';
		$paths = glob($assetsDir . '/assets/intro/*.jpg');
		if (empty($paths)) {
			throw new \RuntimeException("Could not find intro images.");
		}
		return array_map([$this, 'fetchImageInfo'], $paths);
	}

}
