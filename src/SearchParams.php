<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

class SearchParams
{
	private string $collection = 'default';
	private string $keyword = '';

	public static function create(): static
	{
		return new static();
	}

	public function getCollection(): string
	{
		return $this->collection;
	}

	public function setCollection(string $collection): SearchParams
	{
		$this->collection = $collection;
		return $this;
	}

	public function getKeyword(): string
	{
		return $this->keyword;
	}

	public function setKeyword(string $keyword): SearchParams
	{
		$this->keyword = $keyword;
		return $this;
	}
}