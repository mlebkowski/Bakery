<?php namespace Markdown\oEmbed\Provider;
use Markdown\oEmbed\Fetcher\Factory as Factory;

class Provider {
	private $_schemes = Array();
	private $_endpoint;
	private $_fetcher;

	public function __construct($endpoint, $schemes) {
		$this->_schemes = (array) $schemes;
		$this->_endpoint = $endpoint;
	}
	
	public function matchUrl($url) {
		foreach ($this->_schemes as $scheme) {
			if (fnmatch($scheme, $url)) return true;	
		}
		return false;
	}
	
	public function setFetcher(Fetcher $fetcher) {
		$this->_fetcher = $fetcher;
	}
	
	public function getFetcher() {
		if (!$this->_fetcher) {
			$this->_fetcher = Factory::buildFetcher($this->_endpoint);
		}
		return $this->_fetcher;	
	}
}

