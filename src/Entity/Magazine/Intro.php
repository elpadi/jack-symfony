<?php
namespace App\Entity\Magazine;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Entity\Images;

class Intro {

	use Images;

	public function fetchImages() {
		$assetsDir = ($_ENV['PUBLIC_DIR'] ?? dirname(__DIR__, 3)) . '/assets';
		$paths = glob($assetsDir . '/intro/*.jpg');
		if (empty($paths)) {
			throw new \RuntimeException("Could not find intro images in $assetsDir.");
		}
		return array_map([$this, 'fetchImageInfo'], $paths);
	}

}
