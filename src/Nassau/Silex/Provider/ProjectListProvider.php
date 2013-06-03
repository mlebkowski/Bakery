<?php

namespace Nassau\Silex\Provider;

use Nassau\Bakery\Dsn;
use Nassau\Bakery\Project;
use Nassau\Bakery\ProjectsCollection;
use Nassau\Bakery\PullSource;
use Nassau\Bakery\PushDestination;
use Silex\ServiceProviderInterface;
use Silex\Application;

class ProjectListProvider implements ServiceProviderInterface
{
	protected $config;

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app)
	{
		$this->config = $app['Projects'];
		$app['ProjectsCollection'] = $app->share(function (Application $app) {
			return new ProjectsCollection;
		});
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registers
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app)
	{
		/** @var ProjectsCollection $collection */
		$collection = $app['ProjectsCollection'];

		foreach ($this->config as $name => $item)
		{
			$project = $this->createProjectFromConfiguration($name, $item);
			$collection->offsetSet($name, $project);
		}
	}

	/**
	 * @param string $name
	 * @param array $item
	 *
	 * @return Project
	 */
	private function createProjectFromConfiguration($name, $item)
	{
		$project = new Project;
		$project->setName($name);

		if (isset($item['template']))
		{
			$project->setTemplate($item['template']);
			unset($item['template']);
		}

		foreach ((array) $item['pull'] as $prefix => $dsn)
		{
			$dsn = new Dsn($dsn);

			$pull = new PullSource($dsn, is_numeric($prefix) ? null : $prefix);
			$project->getPullSources()->append($pull);
		}
		unset($item['pull']);

		foreach ((array) $item['push'] as $dsn)
		{
			$dsn = new Dsn($dsn);

			$push = new PushDestination($dsn);
			$project->getPushDestinations()->append($push);
		}
		unset($item['push']);

		$project->setOptions($item);

		return $project;
	}

}