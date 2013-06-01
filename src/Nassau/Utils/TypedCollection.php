<?php

namespace Nassau\Utils;

abstract class TypedCollection extends \ArrayObject
{
	public function exchangeArray($input)
	{
		$this->assertType($input);
		parent::exchangeArray($input);
	}

	public function append($value)
	{
		$this->assertType(array($value));
		parent::append($value);
	}

	public function offsetSet($index, $value)
	{
		$this->assertType(array($value));
		parent::offsetSet($index, $value);
	}

	/**
	 * @param array $input
	 *
	 * @throws \InvalidArgumentException
	 */
	private function assertType(array $input)
	{
		$type = $this->getInterfaceName();

		foreach ($input as $item) {
			if (false === ($item instanceof $type)) {
				throw new \InvalidArgumentException("Item is not an instance of $type");
			}
		}
	}

	abstract protected function getInterfaceName();

}