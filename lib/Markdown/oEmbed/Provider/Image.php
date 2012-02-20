<?php namespace Markdown\oEmbed\Provider;
use Markdown\oEmbed\Fetcher\Image as ImageFetcher;

class Image extends Provider {
	public function matchUrl($url) {
		return preg_match('/\.(png|jpe?g|gif)$/i', $url);
	}
	public function getFetcher() {
		return new ImageFetcher(null);
	}	
}
