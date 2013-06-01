<?php

namespace Nassau\Bakery;

use Nassau\Utils\TypedCollection;

class PushDestinationCollection extends TypedCollection
{
	protected function getInterfaceName()
	{
		return __NAMESPACE__ . '\\PushDestinationInterface';
	}

}