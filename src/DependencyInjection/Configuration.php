<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('opendxp_fulltext_search');

		return $treeBuilder;
	}
}
