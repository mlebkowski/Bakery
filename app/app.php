<?php

use Dropbox\AccessToken;
use Dropbox\AccessType;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use Nassau\Silex\Provider\ProjectListProvider;

include dirname(__DIR__) . '/vendor/autoload.php';

$app = new Nassau\Silex\Application();

$app->register(new ConfigServiceProvider(dirname(__DIR__) . '/etc/application.yaml'), array (
	'root_path' => dirname(__DIR__),
	'data_path' => dirname(__DIR__) . '/data',
	'src_path' => dirname(__DIR__) . '/src',
));

$app->register(new ConsoleServiceProvider, array(
	'console.name'              => 'Bakery',
	'console.version'           => '1.0.0',
	'console.project_directory' => dirname(__DIR__)
));

$app->register(new ProjectListProvider);

$app['dropbox-sdk'] = $app->share(function ($app) {

	$ua = sprintf("%s %s", $app['console.name'], $app['console.version']);
	$info = new Dropbox\AppInfo($app['Dropbox']['key'], $app['Dropbox']['secret'], AccessType::AppFolder());
	$config = new Dropbox\Config($info, $ua);

	return function(AccessToken $accessToken) use ($config)
	{
		return new Dropbox\Client($config, $accessToken);
	};
});

$app['debug'] = true;

return $app;

