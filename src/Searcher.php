<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use PDO;
use Try2catch\OpenDxp\FulltextSearchBundle\Model\SearchResultItem;

class Searcher
{
	public function search(SearchParams $searchParams): array
	{
		$dbPath = DbPath::get($searchParams->getCollection());

		if (!file_exists($dbPath))
		{
			return [];
		}

		$db = new PDO('sqlite:' . $dbPath);

		$keyword = $this->escapeFtsQuery($searchParams->getKeyword());

		if (empty($keyword))
		{
			return [];
		}

		$stmt = $db->prepare(<<<SQL
		SELECT id, url, title, description, payload
		FROM documents
		WHERE content MATCH ?
		ORDER BY bm25(documents) ASC
		LIMIT ?
		OFFSET ?
		SQL);
		$stmt->execute([
			$keyword,
			$searchParams->getLimit() ?? 10,
			$searchParams->getOffset() ?? 0,
		]);

		return array_map(
			fn(array $data) => new SearchResultItem(
				id: $data['id'],
				url: $data['url'],
				title: $data['title'] ?? '',
				description: $data['description'] ?? '',
				payload: $data['payload'] ?? null,
			),
			$stmt->fetchAll(PDO::FETCH_ASSOC)
		);
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
