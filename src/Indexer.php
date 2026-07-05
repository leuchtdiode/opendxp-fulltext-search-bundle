<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

use PDO;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class Indexer
{
	/**
	 * @var DocumentProviderInterface[]
	 */
	private iterable $providers;

	public function __construct(
		#[TaggedIterator('fulltext_search.document_provider')] iterable $providers
	)
	{
		$this->providers = $providers;
	}

	public function index(IndexParams $params): void
	{
		$dbPath = DbPath::get($params->getCollection());

		$tmpDbPath = $dbPath . '.' . uniqid();

		$db = new PDO('sqlite:' . $tmpDbPath);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db->exec("DROP TABLE IF EXISTS documents");
		$db->exec("CREATE VIRTUAL TABLE documents USING fts5(id UNINDEXED, url UNINDEXED, title, description, content, payload UNINDEXED, tokenize = 'trigram')");

		$stmt = $db->prepare("INSERT INTO documents (id, url, title, description, content, payload) VALUES (?, ?, ?, ?, ?, ?)");

		foreach ($this->providers as $provider)
		{
			if ($provider->getCollection() !== $params->getCollection())
			{
				continue;
			}

			$documents = $provider->get();

			foreach ($documents as $document)
			{
				$stmt->execute([
					$document->getId(),
					$document->getUrl(),
					$document->getTitle(),
					$document->getDescription(),
					$document->getContent(),
					$document->getPayload(),
				]);
			}
		}

		rename($tmpDbPath, $dbPath);
	}
}
