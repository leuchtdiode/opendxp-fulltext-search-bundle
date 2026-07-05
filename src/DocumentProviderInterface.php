<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use Try2catch\OpenDxp\FulltextSearchBundle\Model\FulltextSearchDocument;
use Iterator;

interface DocumentProviderInterface
{
	/**
	 * @return Iterator<FulltextSearchDocument>
	 */
	public function get(): Iterator;

	public function getCollection(): string;
}
