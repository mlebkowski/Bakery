<?php

use Dropbox\AccessToken;
use Dropbox\AccessType;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use Nassau\Silex\Provider\MarkdownProvider;
use Nassau\Silex\Provider\ProjectListProvider;
use Nassau\Silex\Provider\StorageFactoryProvider;

include dirname(__DIR__) . '/vendor/autoload.php';

$app = new Nassau\Silex\Application();

$app->register(new ConfigServiceProvider(dirname(__DIR__) . '/etc/application.yaml'), array (
	'path.root' => dirname(__DIR__),
	'path.data' => dirname(__DIR__) . '/data',
	'path.src' => dirname(__DIR__) . '/src',
	'path.cache' => dirname(__DIR__) . '/cache',
	'path.config' => dirname(__DIR__) . '/etc',
));

$app->register(new ConsoleServiceProvider, array(
	'console.name' => 'Bakery',
	'console.version' => '1.0.0',
	'console.project_directory' => dirname(__DIR__)
));

$app->register(new ProjectListProvider, array (
	'bakery.project-list.config_file' => $app['path.config'] . '/projects.yaml',
));
$app->register(new StorageFactoryProvider, array (
	'bakery.storage-factory.config_file' => $app['path.config'] . '/storage.yaml',
));
$app->register(new MarkdownProvider, array (
	'markdown.parser.version_timestamp' => new \DateTime($app['Env']['date']),
	'markdown.oembed.config_file' => $app['path.config'] . '/oembed.yaml'
));

$app->register(new Silex\Provider\MonologServiceProvider, array(
	'monolog.logfile' => $app['path.cache'] . strftime('/bakery-%Y-%m-%d.log'),
//	'monolog.logfile' => STDOUT,
	'monolog.name' => strtolower($app['console.name']),
));


$app['dropbox-sdk'] = $app->share(function ($app) {

	$ua = sprintf("%s %s", $app['console.name'], $app['console.version']);
	$info = new Dropbox\AppInfo($app['Dropbox']['key'], $app['Dropbox']['secret'], AccessType::AppFolder());
	$config = new Dropbox\Config($info, $ua);

	return function (AccessToken $accessToken) use ($config) {
		return new Dropbox\Client($config, $accessToken);
	};

});

$app['cache-factory'] = $app->share(function ($app) {

	$baseDir = $app['path.cache'];
	$invalidateBefore = new DateTime($app['Env']['date']);

	return function ($type) use ($baseDir, $invalidateBefore, $app) {
		$key = sprintf('cache-factory.%s', $type);
		if (false === isset($caches[$key])) {
			$app[$key] = new \Nassau\Cache\Cache(sprintf('%s/%s', $baseDir, $type), "", $invalidateBefore);
		}
		return $app[$key];
	};

});

$app['bakery.fetcher.dropbox'] = function () {
	return new \Nassau\Bakery\Fetcher\DropboxFetcher;
};
$app['bakery.fetcher.git'] = function ($app) {
	$path = $app['path.cache'] . '/git';
	is_dir($path) || mkdir($path);
	return new \Nassau\Bakery\Fetcher\GitFetcher($path);
};
$app['bakery.fetcher.filesystem'] = function () {
	return new \Nassau\Bakery\Fetcher\FilesystemFetcher;
};

$app['bakery.fetcher-factory'] = $app->share(function ($app) {

	$factory = new \Nassau\Bakery\Fetcher\FetcherFactory;
	foreach (array($factory::TYPE_FILESYSTEM, $factory::TYPE_GIT) as $type) {
		$factory->registerClosure($type, function () use ($app, $type) {
			return $app['bakery.fetcher.' . $type];
		});
	}
	$factory->registerClosure($factory::TYPE_DROPBOX, function ($key, $secret) use ($app) {
		$accessToken = new AccessToken($key, $secret);
		$closure = $app['dropbox-sdk'];
		/** @var \Dropbox\Client $client */
		$client = $closure($accessToken);

		/** @var \Nassau\Bakery\Fetcher\DropboxFetcher $fetcher */
		$fetcher = $app['bakery.fetcher.dropbox'];
		$fetcher->setClient($client);
		return $fetcher;

	});
	return $factory;
});

$app['bakery.parser'] = $app->share(function (\Nassau\Silex\Application $app)
{
	/** @var \Markdown\Parser $markdown */
	$markdown = $app['markdown.parser'];
	$date = new \DateTime($app['Env']['date']);

	$parser = new \Nassau\Bakery\Parser\Parser($markdown, $date);
	$parser->setCache($app->factoryCache('markdown'));
	return $parser;
});

$app['bakery.indexer'] = $app->share(function (\Nassau\Silex\Application $app)
{
	/** @var \Nassau\Bakery\Fetcher\FetcherFactoryInterface $fetcherFactory */
	$fetcherFactory = $app['bakery.fetcher-factory'];
	/** @var Closure $storageFactor */
	$storageFactor = $app['bakery.storage-factory'];
	/** @var \Monolog\Logger $monolog */
	$monolog = $app['monolog'];

	/** @var \Nassau\Bakery\Parser\Parser $parser */
	$parser = $app['bakery.parser'];

	$indexer = new \Nassau\Bakery\Indexer($fetcherFactory, $storageFactor, $parser);
	$indexer->setLogger($monolog);

	return $indexer;
});

$app['debug'] = true;

return $app;
