<?php

namespace Nassau\Silex\Provider;

use Nassau\Bakery\ProjectInterface;
use Nassau\Bakery\Storage\Storage;
use Nassau\Bakery\Storage\TableDefinition;
use Silex\Application;
use Silex\ServiceProviderInterface;

class StorageFactoryProvider implements ServiceProviderInterface
{
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
		$app['bakery.storage-factory'] = function (Application $app) {
			return function (ProjectInterface $project) use ($app) {
				$name = $project->getName();
				$key = 'bakery.storage-factory.'. $name;

				if (isset($app[$key]))
				{
					return $app[$key];
				}

				$dsn = sprintf('sqlite:%s/%s.db', $app['path.data'], $project->getName());
				$pdo = new \PDO($dsn, null, null, array (
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
				));

				$storage = new Storage($pdo);

				foreach ($app['bakery.storage-tables'] as $tableDefinition)
				{
					$storage->initTable($tableDefinition);
				}
				$app[$key] = $app->share(function () use ($storage) { return $storage; });
				return $storage;
			};
		};
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
		$app['bakery.storage-tables'] = $app->share(function (Application $app) {
			$tables = array ();
			foreach ($app['Storage']['Tables'] as $name => $columns)
			{
				$table = new TableDefinition;
				$table->setName($name);
				foreach ($columns as $column)
				{
					list ($name, $type) = explode(" ", trim($column));
					$table->addColumn($name, $type);
				}
				$tables[] = $table;
			}
			return $tables;
		});
	}

}