# OpenDxp Fulltext Search Bundle

Symfony bundle for indexing and searching documents with SQLite FTS5.

## What it does

- Collects documents from services that implement `DocumentProviderInterface`
- Builds a per-collection SQLite database in `OPENDXP_PRIVATE_VAR`
- Searches the indexed `content` field with SQLite full-text search
- Returns structured results with `id`, `url`, `title`, `description`, and optional `payload`

## How it works

1. You create one or more document providers.
2. The bundle indexes all documents for a collection into `fulltext-search--<collection>.sqlite`.
3. The searcher queries that database with SQLite FTS5.
4. Results are ranked by `bm25()` and paginated with `limit` and `offset`.

The database is stored at:

```text
OPENDXP_PRIVATE_VAR/fulltext-search--<collection>.sqlite
```

## Requirements

- PHP 8.3+
- `ext-pdo`
- SQLite support in PHP
- `open-dxp/opendxp`

## Install

```bash
composer require leuchtdiode/opendxp-fulltext-search-bundle
```

Then register the bundle in your Symfony app if it is not auto-registered:

```php
// config/bundles.php
return [
    // ...
    Try2catch\OpenDxp\FulltextSearchBundle\OpenDxpFulltextSearchBundle::class => ['all' => true],
];
```

## Create a document provider

Implement `DocumentProviderInterface` and return an `Iterator` of `FulltextSearchDocument` objects.

```php
use ArrayIterator;
use Iterator;
use Try2catch\OpenDxp\FulltextSearchBundle\DocumentProviderInterface;
use Try2catch\OpenDxp\FulltextSearchBundle\Model\FulltextSearchDocument;

final class ArticleDocumentProvider implements DocumentProviderInterface
{
    public function getCollection(): string
    {
        return 'default';
    }

    public function get(): Iterator
    {
        return new ArrayIterator([
            new FulltextSearchDocument(
                id: '123',
                url: '/articles/123',
                title: 'Hello world',
                description: 'Intro article',
                content: 'This is the searchable content',
                payload: json_encode(['category' => 'news']),
            ),
        ]);
    }
}
```

If the service is autoconfigured, the bundle tags it automatically as a document provider.

If you want to register it explicitly in `config/services.yaml`, add the tag yourself:

```yaml
services:
  App\FulltextSearch\ArticleDocumentProvider:
    tags:
      - { name: 'fulltext_search.document_provider' }
```

## Index documents

Use the console command:

```bash
php bin/console fulltext-search:index --collection=default
```

If `--collection` is omitted, the command uses `default`.

## Search documents

Inject `Try2catch\OpenDxp\FulltextSearchBundle\Searcher` and call `search()` with `SearchParams`.

```php
use Try2catch\OpenDxp\FulltextSearchBundle\SearchParams;
use Try2catch\OpenDxp\FulltextSearchBundle\Searcher;

$result = $searcher->search(
    SearchParams::create()
        ->setCollection('default')
        ->setKeyword('hello world')
        ->setLimit(10)
        ->setOffset(0)
);

foreach ($result->getItems() as $item) {
    $item->getId();
    $item->getUrl();
    $item->getTitle();
    $item->getDescription();
    $item->getPayloadDecoded();
}

$total = $result->getTotalCount();
```

## Notes

- Search matches against `content`.
- `title` and `description` are returned, but are not used for matching.
- Payload is stored as a string and can be decoded with `getPayloadDecoded()`.
- Each collection has its own SQLite file.
- Re-indexing replaces the existing database file for that collection.
