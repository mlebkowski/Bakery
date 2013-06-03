<?php

namespace Nassau\Silex\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandler extends AbstractProcessingHandler
{
	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  array $record
	 * @return void
	 */
	protected function write(array $record)
	{
		if (null === $this->output)
		{
			return;
		}

		$verbosity = $this->output->getVerbosity();
		switch ($record['level'])
		{
			case Logger::DEBUG:
				if ($verbosity === OutputInterface::VERBOSITY_VERBOSE)
				{
					$this->output->writeln(sprintf('<info>%s</info>', $record['message']));
				}
				break;
			case Logger::INFO:
			case Logger::NOTICE:
				if ($verbosity > OutputInterface::VERBOSITY_QUIET)
				{
					$this->output->writeln($record['message']);
				}
				break;

			case Logger::WARNING:
			case Logger::ERROR:
			case Logger::ALERT:
			case Logger::CRITICAL:
			case Logger::EMERGENCY:
				$this->output->writeln(sprintf('<error>%s</error>', $record['message']));
				break;
		}
	}

}