<?php namespace Markdown;

class Embed {
	private $oembed;
	public function __construct(oEmbed\oEmbed $oembed) {
		$this->oembed = $oembed;
	}
	
	public function parse($str, $keywords = Array ()) {
		list ($url, $caption) = array_pad(explode("\n", $str, 2), 2, '');
		if (substr($url, 0, 7) == 'http://') {
			$data = $this->oembed->getMetaDataForUrl($url);
			if ($data) {
				$str = $this->markup($data, $url) . "\n" . $caption;
			}
		}
		return $str;
	}
	
	 public function markup($data, $url = '') {
		switch ($data['type']):
		case 'photo':
			$str = '<img src="' . htmlspecialchars($data['url']) . '"';
			if ($data['title']) $str .= ' title="' . htmlspecialchars($title) . '"';
			$str .= ' alt=""/>';
			return $str;

		case 'rich':
		case 'video':
			return $data['html'];
		default: return $url;
		endswitch;
	}
}
