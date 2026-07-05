<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle;

class DbPath
{
	public static function get(string $collection): string
	{
		return OPENDXP_PRIVATE_VAR . '/fulltext-search--' . $collection . '.sqlite';
	}
}