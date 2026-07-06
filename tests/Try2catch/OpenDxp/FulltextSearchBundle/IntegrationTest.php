<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle\Tests;

use ArrayIterator;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Try2catch\OpenDxp\FulltextSearchBundle\DocumentProviderInterface;
use Try2catch\OpenDxp\FulltextSearchBundle\Indexer;
use Try2catch\OpenDxp\FulltextSearchBundle\IndexParams;
use Try2catch\OpenDxp\FulltextSearchBundle\Model\FulltextSearchDocument;
use Try2catch\OpenDxp\FulltextSearchBundle\Searcher;
use Try2catch\OpenDxp\FulltextSearchBundle\SearchParams;

class IntegrationTest extends TestCase
{
	/**
	 * @return array<string, array{string, string, string|null}>
	 */
	public static function provideSearchScenarios(): array
	{
		return [
			'simple match'         => [ 'content', 'content', '1' ],
			'partial match'        => [ 'some long content here', 'content', '1' ],
			'no match'             => [ 'some long content here', 'missing', null ],
			'empty keyword'        => [ 'some long content here', '', null ],
			'keyword with spaces'  => [ 'some long content here', 'long content', '1' ],
			'escapable characters' => [ 'content with "quotes"', 'quotes', '1' ],
			'special characters'   => [ 'content with symbols!@#', 'symbols', '1' ],
		];
	}

	#[DataProvider('provideSearchScenarios')]
	public function testIndexAndSearch(string $indexedContent, string $keyword, ?string $expectedId): void
	{
		if (!defined('OPENDXP_PRIVATE_VAR'))
		{
			define('OPENDXP_PRIVATE_VAR', sys_get_temp_dir());
		}

		$collection = 'integration_test';
		$dbPath     = OPENDXP_PRIVATE_VAR . '/fulltext-search--' . $collection . '.sqlite';

		// Ensure clean state
		if (file_exists($dbPath))
		{
			unlink($dbPath);
		}

		// 1. Indexing
		$document = new FulltextSearchDocument('1', 'url', 'title', 'description', $indexedContent, 'payload');

		$provider = new class([ $document ]) implements DocumentProviderInterface {
			private array $documents;

			public function __construct(array $documents)
			{
				$this->documents = $documents;
			}

			public function get(): Iterator
			{
				return new ArrayIterator($this->documents);
			}

			public function getCollection(): string
			{
				return 'integration_test';
			}
		};

		$indexer = new Indexer([ $provider ]);
		$indexer->index(
			IndexParams::create()
				->setCollection($collection)
		);

		$this->assertFileExists($dbPath);

		// 2. Searching
		$searcher = new Searcher();
		$result   = $searcher->search(
			SearchParams::create()
				->setCollection($collection)
				->setKeyword($keyword)
		);

		if ($expectedId === null)
		{
			$this->assertCount(0, $result->getItems());
			$this->assertEquals(0, $result->getTotalCount());
		}
		else
		{
			$this->assertCount(1, $result->getItems());
			$this->assertEquals(1, $result->getTotalCount());
			$this->assertEquals($expectedId, $result->getItems()[0]->getId());
		}

		// Cleanup
		if (file_exists($dbPath))
		{
			unlink($dbPath);
		}
	}
}
