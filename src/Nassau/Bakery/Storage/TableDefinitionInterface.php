<?php

namespace Nassau\Bakery\Storage;

interface TableDefinitionInterface
{
	/**
	 * @param string $name
	 * @param string $type
	 */
	public function addColumn($name, $type);

	/**
	 * @param string $name
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function toSql();
}