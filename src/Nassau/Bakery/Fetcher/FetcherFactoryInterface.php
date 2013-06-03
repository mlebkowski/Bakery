<?php

namespace Nassau\Bakery\Fetcher;

interface FetcherFactoryInterface
{
	/**
	 * @param string $type
	 *
	 * @return FetcherInterface
	 */
	public function fetcherFactory($type);
}