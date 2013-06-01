<?php

namespace Nassau\Bakery;

use Nassau\Utils\TypedCollection;

class PullSourceCollection extends TypedCollection
{
	protected function getInterfaceName()
	{
		return __NAMESPACE__ . '\\PullSourceInterface';
	}

}