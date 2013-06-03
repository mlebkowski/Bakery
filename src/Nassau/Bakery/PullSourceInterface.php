<?php


namespace Nassau\Bakery;


interface PullSourceInterface
{
	/**
	 * @return DsnInterface
	 */
	public function getDsn();

	/**
	 * @return string
	 */
	public function getFetcherType();

	/**
	 * @return string
	 */
	public function getTargetPrefix();

	/**
	 * @return string
	 */
	public function getUniqueId();
}