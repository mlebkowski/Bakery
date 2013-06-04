<?php

namespace Nassau\Bakery\Parser;

use DateTime;

interface ParserInterface
{
	/**
	 * @param string $item
	 * @param bool   $markupAllowed
	 *
	 * @return ParsedBody
	 */
	public function parse($item, $markupAllowed = true);

	/**
	 * @param DateTime $date
	 *
	 * @return bool
	 */
	public function isFresh(DateTime $date);
}