<?php

namespace Nassau\Bakery\Fetcher;

use Nassau\Bakery\PullSourceInterface;
use Nassau\Bakery\IndexItemInterface;

interface HashedFetcherInterface
{
	/**
	 * @param PullSourceInterface $pullSource
	 * @param string $ifNoneMatch
	 *
	 * @return IndexItemInterface[]
	 */
	public function getIndex(PullSourceInterface $pullSource, $ifNoneMatch);

	/**
	 * @return string
	 */
	public function getLastFetchedHash();
}