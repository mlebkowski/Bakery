<?php

namespace Nassau\Bakery;

class PullSource implements PullSourceInterface
{
	/**
	 * @var Dsn $dsn
	 */
	protected $dsn;

	/**
	 * @var string $targetPrefix
	 */
	protected $targetPrefix;


	public function __construct($dsn, $prefix = null)
	{
		$this->setDsn($dsn);
		if (null !== $prefix)
		{
			$this->setTargetPrefix($prefix);
		}
	}

	/**
	 * @return DsnInterface
	 */
	public function getDsn()
	{
		return $this->dsn;
	}

	/**
	 * @param DsnInterface $dsn
	 */
	public function setDsn(DsnInterface $dsn)
	{
		$this->dsn = $dsn;
	}


	/**
	 * @return string
	 */
	public function getFetcherType()
	{
		return $this->dsn->getScheme();
	}

	/**
	 * @return string
	 */
	public function getTargetPrefix()
	{
		return $this->targetPrefix;
	}

	/**
	 * @param string $targetPrefix
	 */
	public function setTargetPrefix($targetPrefix)
	{
		$this->targetPrefix = $targetPrefix;
	}

}