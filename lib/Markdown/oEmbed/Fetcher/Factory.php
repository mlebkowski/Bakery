<?php namespace Markdown\oEmbed\Fetcher;

class Factory {
	public static function buildFetcher($endpoint) {
		return new Curl($endpoint);
	}
}

