<?php


namespace Nassau\Bakery;


interface PushDestinationInterface
{
	/**
	 * @return string
	 */
	public function getPusherType();

	/**
	 * @return DsnInterface
	 */
	public function getDsn();
}