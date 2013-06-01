<?php

namespace Nassau\Silex\Command;

use Dropbox\AccessToken;
use Nassau\Bakery\ProjectsCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectList extends BaseCommand
{
	protected function configure()
	{
		$this
			->setName('bakery:list')
			->setDescription('Print the projects list')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$app = $this->getSilexApplication();
		$projects = $app->getProjects();
		foreach ($projects->getArrayCopy() as $project)
		{
			$output->writeln(sprintf('<info>Project %s:</info>', $project->getName()));
			$output->writeln("<comment>  Pulls:</comment>");
			foreach ($project->getPullSources() as $pull)
			{
				$output->writeln('   - ' . $pull->getDsn()->getUrl());

				$dropboxClient = $app->getDropboxClient($pull->getDsn()->getUsername(), $pull->getDsn()->getPassword());
				var_dump($dropboxClient->getAccountInfo());
			}
		}
	}


}