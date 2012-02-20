<?php namespace Markdown\oEmbed\Fetcher;

class Image extends Fetcher {
	public function fetch($url, $options = Array ()) {
		list ($width, $height) = getimagesize($url);
		
		$ratio = 1;
		if ($width*$height != 0) foreach (Array ('width', 'height') as $key) {
			if (isset($options['max' . $key])) {
				$ratio = min($ratio, $options['max' . $key] / $$key);
			}	
		}
		if ($ratio < 1) {
			$width *= $ratio;
			$height *= $ratio;	
		}
		
		return Array (
			'version' => '1.0',
			'type' => 'photo',
			'width' => (int)$width,
			'height' => (int)$height,
			'title' => '',
			'url' => $url
		);	
	}	
}

