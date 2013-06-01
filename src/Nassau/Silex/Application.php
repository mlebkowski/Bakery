<?php

namespace Nassau\Silex;

use Dropbox\AccessToken;
use Nassau\Bakery\ProjectsCollection;

class Application extends \Silex\Application
{
	/**
	 * @return \Knp\Console\Application
	 */
	public function getConsole()
	{
		return $this['console'];
	}

	/**
	 * @return ProjectsCollection
	 */
	public function getProjects()
	{
		/** @var ProjectsCollection $p */
		$p = $this['ProjectsCollection'];
		return $p;
	}

	/**
	 * @param string $key
	 * @param string $secret
	 *
	 * @return \Dropbox\Client
	 */
	public function getDropboxClient($key, $secret)
	{
		$accessToken = new AccessToken($key, $secret);
		$closure = $this['dropbox-sdk'];
		/** @var \Dropbox\Client $client */
		$client = $closure($accessToken);
		return $client;
	}

}