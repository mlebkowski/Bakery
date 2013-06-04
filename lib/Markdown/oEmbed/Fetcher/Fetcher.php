<?php

namespace Markdown\oEmbed\Fetcher;

abstract class Fetcher {
	protected $_endpoint;
	
	public function __construct($endpoint) {
		$this->_endpoint = $endpoint;
	}
	
	abstract public function fetch($url, $options = array ());
}

