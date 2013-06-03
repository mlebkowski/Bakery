<?php

namespace Nassau\Bakery;

use Symfony\Component\HttpFoundation\ParameterBag;

interface DsnInterface
{
	/**
	 * @param string $dsn
	 */
	public function setDsn($dsn);

	/**
	 * @return string
	 */
	public function getUrl();

	/**
	 * @return string
	 */
	public function getScheme();

	/**
	 * @return string
	 */
	public function getUsername();

	/**
	 * @return string
	 */
	public function getPassword();

	/**
	 * @return string
	 */
	public function getHost();

	/**
	 * @return string
	 */
	public function getPort();

	/**
	 * @return string
	 */
	public function getPath();

	/**
	 * @return ParameterBag
	 */
	public function getQuery();
}