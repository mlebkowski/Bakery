<?php

namespace Nassau\Bakery;

interface FetcherInterface
{

	public function setDsn(DsnInterface $dsn);

	/**
	 * @param string $path
	 *
	 * @return IndexItemInterface[]
	 */
	public function getPathIndex($path = "/");

}