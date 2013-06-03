<?php

namespace Nassau\Bakery\Fetcher;

use Nassau\Bakery\PullSourceInterface;
use Nassau\Bakery\IndexItemInterface;

interface FetcherInterface
{

	/**
	 * @param PullSourceInterface $pullSource
	 *
	 * @return IndexItemInterface[]
	 */
	public function getIndex(PullSourceInterface $pullSource);

	/**
	 * @param string $path
	 * @return string mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getContents($path);

}