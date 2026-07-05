<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle\Command;

use Try2catch\OpenDxp\FulltextSearchBundle\Indexer;
use Try2catch\OpenDxp\FulltextSearchBundle\IndexParams;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'fulltext-search:index')]
class FulltextSearchIndexCommand extends Command
{
	private const OPTION__COLLECTION = 'collection';

	private Indexer $indexer;

	public function __construct(Indexer $indexer)
	{
		$this->indexer = $indexer;
		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setDescription('Indexes documents for fulltext search')
			->addOption(
				self::OPTION__COLLECTION,
				null,
				InputOption::VALUE_REQUIRED,
				'Collection (Default: "default")'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$io->note('Indexing documents...');

		try
		{
			$this->indexer->index(
				IndexParams::create()
					->setCollection($input->getOption(self::OPTION__COLLECTION) ?? 'default')
			);

			$io->success('Documents indexed successfully.');
		}
		catch (Exception $e)
		{
			$io->error('Failed to index documents: ' . $e->getMessage());
			return Command::FAILURE;
		}

		return Command::SUCCESS;
	}
}
