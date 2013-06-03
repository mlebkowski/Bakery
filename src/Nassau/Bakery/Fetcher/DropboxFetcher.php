<?php

namespace Nassau\Bakery\Fetcher;

use DateTime;
use Dropbox\Client;
use Nassau\Bakery\PullSourceInterface;
use Nassau\Bakery\IndexItem;

class DropboxFetcher implements FetcherInterface, HashedFetcherInterface
{
	/**
	 * @var \Dropbox\Client
	 */
	protected $client;

	protected $lastFetchedHash;

	/**
	 * @param \Dropbox\Client $client
	 */
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @return string
	 */
	public function getLastFetchedHash()
	{
		return $this->lastFetchedHash;
	}

	/**
	 * @param PullSourceInterface $pullSource
	 * @param string $ifNoneMatch
	 *
	 * @throws \RuntimeException
	 * @return IndexItem[]
	 */
	public function getIndex(PullSourceInterface $pullSource, $ifNoneMatch = null)
	{
		if (null === $this->client)
		{
			throw new \RuntimeException('Dropbox client is not initialized');
		}

		$path = trim($pullSource->getDsn()->getPath(), '/') . '/';

		if ($ifNoneMatch)
		{
			list ($updated, $list) = $this->client->getMetadataWithChildrenIfChanged($path, $ifNoneMatch);
			if (false === $updated)
			{
				$this->lastFetchedHash = $ifNoneMatch;
				return null;
			}
		}
		else
		{
			$list = $this->client->getMetadataWithChildren($path);
		}

		if (false === isset($list['contents']))
		{
			$this->lastFetchedHash = null;
			return array ();
		}
		$this->lastFetchedHash = $list['hash'];

		$result = array ();
		foreach ($list['contents'] as $file)
		{
			if ($file['is_dir'])
			{
				continue;
			}

			$item = new IndexItem;
			$item->setCreationDate(new DateTime($file['client_mtime']));
			$item->setModificationDate(new DateTime($file['modified']));
			$item->setResourceUrl($file['path']);
			$item->setRelativePath(substr(dirname($file[$path]), strlen($path)));
			$item->setETag($file['rev']);
			$result[] = $item;
		}
		return $result;
	}

	/**
	 * @param string $path
	 * @return string mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getContents($path)
	{
		$stream = fopen('php://memory', 'r+');
		$this->client->getFile($path, $stream);
		fseek($stream, 0);
		return stream_get_contents($stream);
	}

}