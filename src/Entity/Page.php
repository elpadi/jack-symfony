<?php
namespace App\Entity;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

trait Page {

	protected function getDefaultPageData(): array
	{
		$images = new PathPackage('/img', new EmptyVersionStrategy());
		return [
			'site' => [
				'logo' => $images->getUrl('logo.svg'),
				'icons' => [
					'fb' => $images->getUrl('icons/fb.svg'),
					'twitter' => $images->getUrl('icons/twitter.svg'),
					'instagram' => $images->getUrl('icons/instagram.svg'),
				],
			],
		];
	}

}
