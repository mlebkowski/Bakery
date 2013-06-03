<?php

namespace Nassau\Bakery;

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

	// TODO: add logger;

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
		$fetcher = $this->createFetcher($source);

		if ($fetcher instanceof HashedFetcherInterface && false === $force)
		{
			$hash = $storage->getHash($source->getUniqueId());
			$index = $fetcher->getIndex($source, $hash);
		}
		else
		{
			$index = $fetcher->getIndex($source);
		}

		if (null === $index)
		{
			return;
		}

		if ($fetcher instanceof HashedFetcherInterface)
		{
			$hash = $fetcher->getLastFetchedHash();
			if ($hash)
			{
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
		foreach ($index as $item) {
			$exists = $storage->getBySlug($item->getSlug());
			if ($exists) {
				$reparse = 0;
				// TODO: this is parser dependent
//				if (new \DateTime($exists['last_indexed']) < "parser date")
//				{
//					$reparse = 1;
//				}
				$storage->update($item->getSlug(), $item->getETag(), $item->getModificationDate(), $reparse);
			} else {
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