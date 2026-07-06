<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use PDO;
use Try2catch\OpenDxp\FulltextSearchBundle\Model\SearchResult;
use Try2catch\OpenDxp\FulltextSearchBundle\Model\SearchResultItem;

class Searcher
{
	public function search(SearchParams $searchParams): SearchResult
	{
		$dbPath = DbPath::get($searchParams->getCollection());

		if (!file_exists($dbPath))
		{
			return new SearchResult([], 0);
		}

		$db = new PDO('sqlite:' . $dbPath);

		$keyword = $this->escapeFtsQuery($searchParams->getKeyword());

		if (empty($keyword))
		{
			return new SearchResult([], 0);
		}

		$stmt = $db->prepare(<<<SQL
		SELECT id, url, title, description, payload
		FROM documents
		WHERE content MATCH ?
		ORDER BY bm25(documents) ASC, id ASC
		LIMIT ?
		OFFSET ?
		SQL
		);
		$stmt->execute([
			$keyword,
			$searchParams->getLimit() ?? 10,
			$searchParams->getOffset() ?? 0,
		]);

		$stmtCount = $db->prepare(<<<SQL
		SELECT COUNT(*)
		FROM documents
		WHERE content MATCH ?
		SQL
		);
		$stmtCount->execute([$keyword]);
		$totalCount = (int) $stmtCount->fetchColumn();

		$items = [];

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($results as $data)
		{
			$items[] = new SearchResultItem(
				id: $data['id'],
				url: $data['url'],
				title: $data['title'] ?? '',
				description: $data['description'] ?? '',
				payload: $data['payload'] ?? null,
			);
		}

		return new SearchResult($items, $totalCount);
	}

	private function escapeFtsQuery(string $keyword): string
	{
		$words        = preg_split('/\s+/', trim($keyword));
		$escapedWords = [];

		foreach ($words as $word)
		{
			if ($word === '')
			{
				continue;
			}

			// 2. Escape existing double quotes by doubling them
			$sanitizedWord = str_replace('"', '""', $word);

			$escapedWords[] = '"' . $sanitizedWord . '"';
		}

		// 5. Join words back together. SQLite defaults to an implicit 'AND' between terms.
		return implode(' ', $escapedWords);
	}
}
