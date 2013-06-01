<?php

namespace Nassau\Bakery;

class Dsn implements DsnInterface
{
	protected $dsn;
	protected $parsed;

	public function __construct($dsn)
	{
		$this->setDsn($dsn);
	}

	/**
	 * @param string $dsn
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setDsn($dsn)
	{
		$this->dsn = $dsn;
		$this->parsed = parse_url($dsn);
		if (false === $this->parsed)
		{
			throw new \InvalidArgumentException("Invalid dsn: $dsn");
		}
		$this->parsed += array (
			'scheme' => null,
			'user' => null,
			'pass' => null,
			'port' => null,
			'host' => null,
			'path' => null,
			'query' => array (),
		);
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->dsn;
	}


	/**
	 * @return string
	 */
	public function getScheme()
	{
		return $this->parsed['scheme'];
	}


	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->parsed['user'];
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->parsed['pass'];
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->parsed['host'];
	}

	/**
	 * @return string
	 */
	public function getPort()
	{
		return $this->parsed['port'];
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->parsed['path'];
	}


}