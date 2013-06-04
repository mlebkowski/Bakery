<?php

namespace Nassau\Silex\Provider;

use Markdown\Embed;
use Markdown\oEmbed\oEmbed;
use Markdown\Parser;
use Silex\Application;

class MarkdownProvider extends AbstractConfigBasedProvider
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
		$app['markdown.oembed'] = $app->share(function (){
			return new oEmbed;
		});
		$app['markdown.oembed.provider_class'] = '\Markdown\oEmbed\Provider\Provider';

		$app['markdown.parser'] = $app->share(function ($app)
		{
			return new Parser(new Embed($app['markdown.oembed']));
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
		/** @var oEmbed $oEmbed */
		$oEmbed = $app['markdown.oembed'];
		$config = $this->getConfig($app['markdown.oembed.config_file']);

		foreach ($config as $provider)
		{
			$provider = new $app['markdown.oembed.provider_class']($provider['Endpoint'], $provider['Schemes']);
			$oEmbed->addProvider($provider);
		}
	}

}