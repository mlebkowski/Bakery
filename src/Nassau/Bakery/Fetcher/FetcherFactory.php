<?php

namespace Nassau\Bakery\Fetcher;

class FetcherFactory implements FetcherFactoryInterface
{
	const TYPE_DROPBOX = 'dropbox';
	const TYPE_FILESYSTEM = 'filesystem';
	const TYPE_GIT = 'git';

	protected $closures = array ();

	/**
	 * @param string $type
	 * @param callable $closure
	 */
	public function registerClosure($type, \Closure $closure)
	{
		$this->closures[$type] = $closure;
	}

	/**
	 * @param string $type
	 *
	 * @throws \InvalidArgumentException
	 * @return FetcherInterface
	 */
	public function fetcherFactory($type)
	{
		if (false === isset($this->closures[$type]))
		{
			throw new \InvalidArgumentException("$type is not a recognized fetcher type");
		}

		$closure = $this->closures[$type];
		$args = func_get_args();
		return call_user_func_array($closure, array_slice($args, 1));
	}

}