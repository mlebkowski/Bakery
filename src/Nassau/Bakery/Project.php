<?php

namespace Nassau\Bakery;

use Symfony\Component\HttpFoundation\ParameterBag;

class Project implements ProjectInterface
{
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var ParameterBag $options;
	 */
	protected $options;

	/**
	 * @var PullSourceCollection
	 */
	protected $pullSources;
	/**
	 * @var PushDestinationCollection
	 */
	protected $pushDestinations;

	public function __construct()
	{
		$this->pullSources = new PullSourceCollection;
		$this->pushDestinations = new PushDestinationCollection;
		$this->options = new ParameterBag;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options->replace($options);
	}

	/**
	 * @return ParameterBag
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return \Nassau\Bakery\PullSourceCollection
	 */
	public function getPullSources()
	{
		return $this->pullSources;
	}

	/**
	 * @return \Nassau\Bakery\PushDestinationCollection
	 */
	public function getPushDestinations()
	{
		return $this->pushDestinations;
	}

}