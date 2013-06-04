<?php

namespace Nassau\Bakery\Parser;

class ParsedBody implements ParsedBodyInterface
{
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var string;
	 */
	protected $contents;

	/**
	 * @param string $contents
	 */
	public function setContents($contents)
	{
		$this->contents = $contents;
	}

	/**
	 * @return string
	 */
	public function getContents()
	{
		return $this->contents;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

}