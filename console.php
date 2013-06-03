#!/usr/bin/env php
<?php

/** @var Nassau\Silex\Application $app */
$app = require __DIR__ . '/app/app.php';

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

$console->run();
