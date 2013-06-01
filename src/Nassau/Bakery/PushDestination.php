<?php

namespace Nassau\Bakery;

class PushDestination implements PushDestinationInterface
{
	/**
	 * @var DsnInterface $dsn
	 */
	protected $dsn;

	public function __construct(DsnInterface $dsn)
	{
		$this->setDsn($dsn);
	}

	/**
	 * @return string
	 */
	public function getPusherType()
	{
		return $this->dsn->getScheme();
	}

	/**
	 * @return DsnInterface
	 */
	public function getDsn()
	{
		return $this->dsn;
	}

	/**
	 * @param \Nassau\Bakery\DsnInterface $dsn
	 */
	public function setDsn(DsnInterface $dsn)
	{
		$this->dsn = $dsn;
	}

}