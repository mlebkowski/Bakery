<?php

namespace Markdown\oEmbed;

use Markdown\oEmbed\Provider\Provider;

class oEmbed
{
	/**
	 * @var Provider[]
	 */
	protected $_providers = array ();
	
	
	public $discovery = false;
	
	public function getMetaDataForUrl($url, $options = array()) {
		$provider = $this->getProviderForUrl($url);
		if (!$provider) if (!$this->discovery) return null;
		else {
			// try to parse & discover the endpoint
			// 1. GET 
			// 2. PARSE
			// 3. CREATE fetcher
			throw new oEmbedNotImplemented();	
		}
		
		return $this->fetchData($provider, $url, $options);
	}
	public function fetchData(Provider $provider, $url, $options) {
		return $provider->getFetcher()->fetch($url, $options);
	}
	
	public function getProviderForUrl($url) {
		foreach ($this->_providers as $provider) {
			if ($provider->matchUrl($url)) {
				return $provider;
			}
		}
		return null;
	}
	
	public function addProvider(Provider $provider) {
		$this->_providers[] = $provider;
	}
	
}

