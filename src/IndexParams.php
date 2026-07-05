<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

class IndexParams
{
	private string $collection = 'default';

	public static function create(): static
	{
	    return new static();
	}

	public function getCollection(): string
	{
		return $this->collection;
	}

	public function setCollection(string $collection): IndexParams
	{
		$this->collection = $collection;
		return $this;
	}
}