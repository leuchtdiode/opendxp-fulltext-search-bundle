<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

class SearchParams
{
	private string $collection = 'default';
	private string $keyword    = '';
	private ?int   $offset     = null;
	private ?int   $limit      = null;

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

	public function getOffset(): ?int
	{
		return $this->offset;
	}

	public function setOffset(?int $offset): SearchParams
	{
		$this->offset = $offset;
		return $this;
	}

	public function getLimit(): ?int
	{
		return $this->limit;
	}

	public function setLimit(?int $limit): SearchParams
	{
		$this->limit = $limit;
		return $this;
	}
}