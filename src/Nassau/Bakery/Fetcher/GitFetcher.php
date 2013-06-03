<?php

namespace Nassau\Bakery\Fetcher;

use Nassau\Bakery\PullSourceInterface;
use Nassau\Bakery\IndexItemInterface;
use Symfony\Component\Process\Process;

class GitFetcher extends FilesystemFetcher implements HashedFetcherInterface
{
	protected $lastFetchedHash;
	protected $workingDir;
	protected $currentDir;

	public function __construct($dir)
	{
		$this->setWorkingDir($dir);
	}

	/**
	 * @param string $dir
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setWorkingDir($dir)
	{
		$this->workingDir = realpath($dir);
		if (false === $this->workingDir)
		{
			throw new \InvalidArgumentException(sprintf('"%s" is not a valid working directory', $dir));
		}
	}

	/**
	 * @param PullSourceInterface $pullSource
	 * @param string $ifNoneMatch
	 *
	 * @return IndexItemInterface[]
	 */
	public function getIndex(PullSourceInterface $pullSource, $ifNoneMatch = null)
	{
		$hash = substr(md5($pullSource->getDsn()->getUrl()), 0, 6);
		$this->currentDir = sprintf('%s/%s', $this->workingDir, $hash);

		if (false === is_dir($this->currentDir))
		{
			mkdir($this->currentDir, 0755, true);
		}

		if (false === is_dir(sprintf('%s/.git', $this->currentDir)))
		{
			list ($url) = explode('?', $pullSource->getDsn()->getUrl());
			$branch = $pullSource->getDsn()->getQuery()->get('branch', 'master');
			$this->execute('clone -b %s %s .', escapeshellarg($branch), escapeshellarg($url));
		}

		$this->execute('pull');
		$sha = trim($this->execute('rev-parse HEAD'));
		$this->lastFetchedHash = $sha;

		if ($ifNoneMatch === $sha)
		{
			return null;
		}

		return $this->indexPath($this->currentDir);
	}

	/**
	 * @return string
	 */
	public function getLastFetchedHash()
	{
		return $this->lastFetchedHash;
	}

	private function execute($command)
	{
		if (func_get_args() > 1)
		{
			$args = func_get_args();
			$command = vsprintf($command, array_slice($args, 1));
		}

		$process = new Process(sprintf('git %s', $command), $this->currentDir);
		$process->setTimeout(60);
		$process->run();

		return $process->getOutput();
	}

	protected function getFileModificationDate($path)
	{
		return new \DateTime($this->execute(
			'log -1 --format="format:%%ci" -- %s',
			escapeshellarg($path)
		));
	}

	protected function getFileCreationDate($path)
	{
		return new \DateTime($this->execute(
			'log --diff-filter=A --format="format:%%ci" -- %s',
			escapeshellarg($path)
		));

	}


}