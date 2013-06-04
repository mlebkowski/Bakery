<?php

namespace Markdown;

class MarkupOptions
{
	protected $allowHtml = true;
	protected $headerLevel = 0;
	protected $tabWidth = 4;
	protected $elementSuffix = '>';

	/**
	 * @param boolean $allowHtml
	 */
	public function setAllowHtml($allowHtml)
	{
		$this->allowHtml = $allowHtml;
	}

	/**
	 * @return boolean
	 */
	public function getAllowHtml()
	{
		return $this->allowHtml;
	}

	/**
	 * @param int $headerLevel
	 */
	public function setHeaderLevel($headerLevel)
	{
		$this->headerLevel = $headerLevel;
	}

	/**
	 * @return int
	 */
	public function getHeaderLevel()
	{
		return $this->headerLevel;
	}

	/**
	 * @param string $elementSuffix
	 */
	public function setElementSuffix($elementSuffix)
	{
		$this->elementSuffix = $elementSuffix;
	}

	/**
	 * @return string
	 */
	public function getElementSuffix()
	{
		return $this->elementSuffix;
	}

	/**
	 * @param int $tabWidth
	 */
	public function setTabWidth($tabWidth)
	{
		$this->tabWidth = $tabWidth;
	}

	/**
	 * @return int
	 */
	public function getTabWidth()
	{
		return $this->tabWidth;
	}

}
