<?php

namespace Nassau\Bakery\Fetcher;

use Nassau\Bakery\IndexItem;
use Nassau\Bakery\IndexItemInterface;
use Nassau\Bakery\PullSourceInterface;

class FilesystemFetcher implements FetcherInterface
{
	/**
	 * @param PullSourceInterface $pullSource
	 *
	 * @return IndexItemInterface[]
	 */
	public function getIndex(PullSourceInterface $pullSource)
	{
		$path = $pullSource->getDsn()->getPath();

		return $this->indexPath($path);
	}

	/**
	 * @param string $path
	 * @return string mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getContents($path)
	{
		return file_get_contents($path);
	}

	/**
	 * @param string $path
	 *
	 * @return \DateTime
	 */
	protected function getFileModificationDate($path)
	{
		return \DateTime::createFromFormat('U', filemtime($path));
	}

	/**
	 * @param string $path
	 *
	 * @return \DateTime
	 */
	protected function getFileCreationDate($path)
	{
		return \DateTime::createFromFormat('U', filectime($path));
	}

	/**
	 * @param string $path
	 *
	 * @return IndexItemInterface[]
	 */
	protected function indexPath($path)
	{
		$result = array ();
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
		/** @var \FilesystemIterator $file */
		foreach ($iterator as $file) {
			if (false === in_array($file->getExtension(), array('md', 'txt', 'text'))) {
				continue;
			}

			$item = new IndexItem;
			$item->setResourceUrl($file->getRealPath());
			$item->setETag(md5_file($file->getRealPath()));
			$item->setModificationDate($this->getFileModificationDate($file->getRealPath()));
			$item->setCreationDate($this->getFileCreationDate($file->getRealPath()));
			$item->setRelativePath(substr($file->getPath(), strlen($path)));
			$result[] = $item;
		}
		return $result;
	}

}