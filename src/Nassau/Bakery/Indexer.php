<?php

namespace Nassau\Bakery;

use Monolog\Logger;
use Nassau\Bakery\Fetcher\FetcherFactory;
use Nassau\Bakery\Fetcher\FetcherInterface;
use Nassau\Bakery\Fetcher\HashedFetcherInterface;
use Nassau\Bakery\Storage\Storage;
use Nassau\Bakery\IndexItemInterface;

class Indexer implements IndexerInterface
{
	/**
	 * @var Fetcher\FetcherFactoryInterface
	 */
	protected $fetcherFactory;

	/**
	 * @var callable
	 */
	protected $storageFactory;

	/**
	 * @var \Monolog\Logger
	 */
	protected $logger;


	public function __construct(Fetcher\FetcherFactoryInterface $fetcherFactory, \Closure $storageFactory)
	{
		$this->fetcherFactory = $fetcherFactory;
		$this->storageFactory = $storageFactory;
	}

	public function rebuildIndex(ProjectInterface $project, $force = false)
	{
		$storage = $this->createStorage($project);

		foreach ($project->getPullSources() as $source)
		{
			$this->reindexSource($source, $storage, $force);
		}

		// TODO: will produce errors with HashedFetcherInterface
		$storage->setDeletedFlag();
	}

	/**
	 * @param \Monolog\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Adds a log record.
	 *
	 * @param string  $message The log message
	 * @param integer $level   The logging level
	 *
	 * @return Boolean Whether the record has been processed
	 */
	protected function log($message, $level = Logger::INFO)
	{
		return $this->logger && $this->logger->addRecord($level, $message);
	}

	/**
	 * @param PullSourceInterface $source
	 *
	 * @return FetcherInterface|HashedFetcherInterface
	 */
	protected function createFetcher(PullSourceInterface $source)
	{
		switch ($source->getFetcherType())
		{
			case FetcherFactory::TYPE_DROPBOX:
				return $this->fetcherFactory->fetcherFactory(
					$source->getFetcherType(),
					$source->getDsn()->getUsername(),
					$source->getDsn()->getPassword()
				);
			default:
				return $this->fetcherFactory->fetcherFactory($source->getFetcherType());
		}
	}

	/**
	 * @param ProjectInterface $project
	 *
	 * @return Storage
	 */
	protected function createStorage(ProjectInterface $project)
	{
		$closure = $this->storageFactory;
		/** @var Storage $storage */
		$storage = $closure($project);
		return $storage;
	}

	/**
	 * @param PullSourceInterface $source
	 * @param Storage $storage
	 * @param bool $force
	 */
	protected function reindexSource(PullSourceInterface $source, Storage $storage, $force = false)
	{
		$this->log(sprintf('Indexing source: %s', $source->getDsn()->getUrl()));
		$fetcher = $this->createFetcher($source);

		if ($fetcher instanceof HashedFetcherInterface && false === $force)
		{
			$hash = $storage->getHash($source->getUniqueId());
			$this->log(sprintf('Using cached content hash: %s', $hash ?: '<comment>empty hash</comment>'), Logger::DEBUG);
			$index = $fetcher->getIndex($source, $hash);
		}
		else
		{
			$index = $fetcher->getIndex($source);
		}

		if (null === $index)
		{
			$this->log('Content has not changed since last time', Logger::DEBUG);
			return;
		}

		if ($fetcher instanceof HashedFetcherInterface)
		{
			$hash = $fetcher->getLastFetchedHash();
			if ($hash)
			{
				$this->log(sprintf('Storing content hash: %s', $hash), Logger::DEBUG);
				$storage->setHash($source->getUniqueId(), $hash);
			}
		}

		$this->storeIndex($index, $storage);

	}

	/**
	 * @param IndexItemInterface[] $index
	 * @param Storage $storage
	 */
	protected function storeIndex($index, Storage $storage)
	{
		$this->log(sprintf('Storing %d items', sizeof($index)));
		foreach ($index as $item) {
			$exists = $storage->getBySlug($item->getSlug());
			if ($exists) {
				$this->log(sprintf(' - updating: %s', $item->getSlug()), Logger::DEBUG);
				$reparse = 0;
				// TODO: this is parser dependent
//				if (new \DateTime($exists['last_indexed']) < "parser date")
//				{
//					$reparse = 1;
//				}
				$storage->update($item->getSlug(), $item->getETag(), $item->getModificationDate(), $reparse);
			} else {
				$this->log(sprintf(' - creating: %s', $item->getSlug()), Logger::DEBUG);
				$storage->create(
					$item->getSlug(),
					$item->getRelativePath(),
					$item->getETag(),
					$item->getCreationDate(),
					$item->getModificationDate()
				);
			}
		}
	}

}