<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use PDO;

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

		$stmt = $db->prepare("SELECT id, url, title, description, payload FROM documents WHERE content MATCH ?");
		$stmt->execute([ $keyword ]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
			$sanitizedWord = str_replace('"', '', $word);

			$escapedWords[] = '"' . $sanitizedWord . '"';
		}

		// 5. Join words back together. SQLite defaults to an implicit 'AND' between terms.
		return implode(' ', $escapedWords);
	}
}
