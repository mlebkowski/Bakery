<?php

namespace Nassau\Bakery\Storage;

class TableDefinition implements TableDefinitionInterface
{
	/**
	 * @var array
	 */
	protected $columns = array ();
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @param string $name
	 * @param string $type
	 */
	public function addColumn($name, $type)
	{
		$this->columns[$name] = $type;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function toSql()
	{
		return sprintf('CREATE TABLE IF NOT EXISTS `%s` (' . $this->getColumnsSql() . ')', $this->getName());
	}

	protected function getColumnsSql()
	{
		if (0 === sizeof($this->columns))
		{
			throw new \RuntimeException('The column list is empty');
		}

		$columns = array ();
		foreach ($this->columns as $name => $type)
		{
			$columns[] = sprintf('%s %s', $name, $type);
		}
		return implode(",\n", $columns);
	}


}