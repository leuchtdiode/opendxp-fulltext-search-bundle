<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle\Model;

class SearchResult
{
	/** @var SearchResultItem[] */
	private array $items;

	private int $totalCount;

	/**
	 * @param SearchResultItem[] $items
	 */
	public function __construct(array $items, int $totalCount)
	{
		$this->items      = $items;
		$this->totalCount = $totalCount;
	}

	/**
	 * @return SearchResultItem[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

	public function getTotalCount(): int
	{
		return $this->totalCount;
	}
}
