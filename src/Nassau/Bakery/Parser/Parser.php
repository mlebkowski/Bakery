<?php

namespace Nassau\Bakery\Parser;

use DateTime;
use Markdown\MarkupOptions;
use Markdown\Parser as Markdown;
use Nassau\Cache\Cache;

class Parser implements ParserInterface
{
	/**
	 * @var DateTime
	 */
	protected $versionTimestamp;

	/**
	 * @var Markdown
	 */
	protected $markdown;

	/**
	 * @var Cache
	 */
	protected $cache;

	public function __construct(Markdown $markdown, \DateTime $versionTimestamp)
	{
		$this->markdown = $markdown;
		$this->setVersionTimestamp($versionTimestamp);
	}

	/**
	 * @param string $text
	 * @param bool   $markupAllowed
	 *
	 * @return ParsedBodyInterface
	 */
	public function parse($text, $markupAllowed = true)
	{
		$options = new MarkupOptions;
		$options->getAllowHtml($markupAllowed);

		$parsed = $this->cache ? $this->cache->load((int) $markupAllowed . $text) : null;
		$parsed = $parsed ?: $this->markdown->transform($text, $options);

		$this->cache && $this->cache->save((int) $markupAllowed . $text, $parsed);

		$body = new ParsedBody;
		$body->setContents($parsed);

		if (preg_match('/<(h\d)(?:\s[^>]+)>(?<title>.*)<\/\1>/', $parsed, $m))
		{
			$title = strip_tags(str_replace(chr(160), ' ', $m['title']));
			$body->setTitle($title);
		}

		return $body;
	}

	/**
	 * @param DateTime $date
	 *
	 * @return bool
	 */
	public function isFresh(DateTime $date)
	{
		return $date >= $this->versionTimestamp;
	}


	/**
	 * @param \DateTime $versionTimestamp
	 */
	public function setVersionTimestamp(DateTime $versionTimestamp)
	{
		$this->versionTimestamp = $versionTimestamp;
	}

	/**
	 * @return \DateTime
	 */
	public function getVersionTimestamp()
	{
		return $this->versionTimestamp;
	}

	/**
	 * @param \Nassau\Cache\Cache $cache
	 */
	public function setCache(Cache $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * @return \Nassau\Cache\Cache
	 */
	public function getCache()
	{
		return $this->cache;
	}



}