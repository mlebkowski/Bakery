<?php

namespace Nassau\Bakery;

interface ProjectInterface
{

	public function getName();
	public function setName($name);

	public function getTemplate();
	public function setTemplate($name);

	public function getOptions();
	public function setOptions(array $options);

	/**
	 * @return PullSourceInterface[]
	 */
	public function getPullSources();

	/**
	 * @return PushDestinationInterface[]
	 */
	public function getPushDestinations();

}