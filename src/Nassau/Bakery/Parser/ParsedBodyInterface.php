<?php

namespace Nassau\Bakery\Parser;

interface ParsedBodyInterface
{
	/**
	 * @param string $contents
	 *
	 * @return void
	 */
	public function setContents($contents);

	/**
	 * @return string
	 */
	public function getContents();

	/**
	 * @param string $title
	 *
	 * @return void
	 */
	public function setTitle($title);

	/**
	 * @return string
	 */
	public function getTitle();

}