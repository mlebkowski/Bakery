<?php

namespace Nassau\Bakery;

use Nassau\Utils\TypedCollection;

class ProjectsCollection extends TypedCollection
{
	/**
	 * @return ProjectInterface[]
	 */
	public function getArrayCopy()
	{
		return parent::getArrayCopy();
	}


	protected function getInterfaceName()
	{
		return __NAMESPACE__ . '\\ProjectInterface';
	}
}