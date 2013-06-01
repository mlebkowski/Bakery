<?php

namespace Nassau\Silex\Command;

use Knp\Command\Command;

class BaseCommand extends Command
{
	/**
	 * @return \Nassau\Silex\Application
	 */
	public function getSilexApplication()
	{
		return $this->getApplication()->getSilexApplication();
	}

	/**
	 * @return \Knp\Console\Application
	 */
	public function getApplication()
	{
		return parent::getApplication();
	}


}