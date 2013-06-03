<?php

namespace Nassau\Silex\Command;



use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexProject extends BaseCommand
{
	const ARGUMENT_PROJECT_NAME = 'project-name';

	protected function configure()
	{
		$this
			->setName('bakery:index')
			->setDescription('Index the specified project')
			->addArgument(self::ARGUMENT_PROJECT_NAME, InputArgument::REQUIRED, 'Project name to index')
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument(self::ARGUMENT_PROJECT_NAME);
		$project = $this->getSilexApplication()->getProjects()->offsetGet($name);

		$indexer = $this->getSilexApplication()->getIndexer();

		$indexer->rebuildIndex($project);

	}

}