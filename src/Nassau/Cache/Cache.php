<?php

namespace Nassau\Cache;

class Cache
{
	/**
	 * @var string
	 */
	protected $cachePath;
	/**
	 * @var string
	 */
	protected $salt;
	/**
	 * @var \DateTime
	 */
	protected $invalidateBefore;

	public function __construct($cachePath, $salt, \DateTime $invalidateBefore = null)
	{
		$this->cachePath = realpath($cachePath);
		if (false === $this->cachePath)
		{
			throw new \InvalidArgumentException('Invalid cache path given: ' . $cachePath);
		}
		$this->salt = $salt;
		$this->invalidateBefore = $invalidateBefore;
	}

	protected function getHash($key)
	{
		return md5(md5($key) . $this->salt);
	}

	protected function getCachePath($key)
	{
		$key = $this->getHash($key);
		return sprintf('%s/%s/%s.cache', $this->cachePath, substr($key, 0, 2), $key);
	}

	protected function isCacheExpired($key, $ttl = 0)
	{
		$cPath = $this->getCachePath($key);
		if (file_exists($cPath) == false)
		{
			return true;
		}

		if ($this->invalidateBefore && filemtime($cPath) < $this->invalidateBefore->getTimestamp())
		{
			return true;

		}
		if ($ttl && strtotime($ttl, filemtime($cPath)) < time())
		{
			return true;
		}

		return false;
	}

	public function save($key, $data)
	{
		$path = $this->getCachePath($key);
		if (false === is_dir(dirname($path)))
		{
			mkdir(dirname($path), 0751, true);
		}
		file_put_contents($path, $data);
	}

	public function load($key, $ttl = 0)
	{
		if ($this->isCacheExpired($key, $ttl))
		{
			return null;
		}

		$cPath = $this->getCachePath($key);
		return file_get_contents($cPath);
	}
}
