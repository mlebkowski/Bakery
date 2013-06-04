<?php

namespace Markdown\oEmbed\Fetcher;

class Curl extends Fetcher {
	public function fetch($url, $options = Array ()) {
		$query = array (
			'url' => $url,
			'format' => 'json',
		);
		foreach (array ('maxwidth', 'maxheight') as $key) {
			if (isset($options[$key])) $query[$key] = (int)$options[$key];
		}
		
		$url = $this->_endpoint . '?' . http_build_query($query);
		$c = curl_init($url);
		curl_setopt_array($c, Array (
			CURLOPT_RETURNTRANSFER => true,
		));
		return json_decode(curl_exec($c), true);
	}	
}

