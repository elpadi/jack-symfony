<?php
namespace App\Entity\Pages;

use App\Entity\Page;
use App\Entity\Cockpit;

class JackBlackPussyCat {

	use Page;
	use Cockpit;

	public function getPageData(): array
	{
		return array_merge_recursive($this->getDefaultPageData(), [
			'page' => $this->fetchCockpitData('collections:findOne', 'pages', ['path' => '/jbpc']),
		]);
	}

}
