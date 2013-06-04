<?php

namespace Nassau\Silex;

use Dropbox\AccessToken;
use Nassau\Bakery\IndexerInterface;
use Nassau\Bakery\ProjectsCollection;
use Nassau\Cache\Cache;

class Application extends \Silex\Application
{
	const CACHE_TYPE_MISC = 'misc';
	const CACHE_TYPE_MARKDOWN = 'markdown';

	/**
	 * @return \Knp\Console\Application
	 */
	public function getConsole()
	{
		return $this['console'];
	}

	/**
	 * @return IndexerInterface
	 */
	public function getIndexer()
	{
		return $this['bakery.indexer'];
	}

	/**
	 * @return ProjectsCollection
	 */
	public function getProjects()
	{
		/** @var ProjectsCollection $p */
		$p = $this['bakery.project-list'];
		return $p;
	}

	/**
	 * @param string $type
	 *
	 * @return Cache
	 */
	public function factoryCache($type)
	{
		$factory = $this['cache-factory'];
		/** @var Cache $cache */
		$cache = $factory($type);
		return $cache;
	}

}