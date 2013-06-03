#!/usr/bin/env php
<?php

use Symfony\Component\Console\Output\ConsoleOutput;

/** @var Nassau\Silex\Application $app */
$app = require __DIR__ . '/app/app.php';

$app['console.output'] = $app->share(function ()
{
	return new ConsoleOutput;
});

$app['monolog.handler'] = $app->share(function ($app) {
	$handler = new \Nassau\Silex\Logger\ConsoleHandler;
	$handler->setOutput($app['console.output']);
	return $handler;
});

$console = $app->getConsole();

$dir = new DirectoryIterator($app['path.src'] . '/Nassau/Silex/Command');
/** @var DirectoryIterator $file */
foreach ($dir as $file)
{
	if ($file->isDot())
	{
		continue;
	}

	$name = basename($file->getFilename(), '.php');
	$className = "\\Nassau\\Silex\\Command\\" . $name;

	try
	{
		$console->add(new $className);
	}
	catch (\LogicException $e)
	{

	}
}

/** @var ConsoleOutput $output */
$output = $app['console.output'];
$console->run(null, $output);
