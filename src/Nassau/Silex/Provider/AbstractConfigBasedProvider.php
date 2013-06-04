<?php

namespace Nassau\Silex\Provider;

use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractConfigBasedProvider implements ServiceProviderInterface
{
	/**
	 * @param string $path
	 *
	 * @return array
	 */
	final protected function getConfig($path)
	{
		return Yaml::parse(file_get_contents($path));
	}
}