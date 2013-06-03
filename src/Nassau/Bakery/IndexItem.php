<?php

namespace Nassau\Bakery;

class IndexItem implements IndexItemInterface
{
	/**
	 * @var \DateTime
	 */
	protected $modificationDate;
	/**
	 * @var \DateTime
	 */
	protected $creationDate;
	/**
	 * @var string
	 */
	protected $etag;
	/**
	 * @var string
	 */
	protected $resourceUrl;
	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $relativePath;

	/**
	 * @param \DateTime $creationDate
	 */
	public function setCreationDate(\DateTime $creationDate)
	{
		$this->creationDate = $creationDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}

	/**
	 * @param string $etag
	 */
	public function setETag($etag)
	{
		$this->etag = $etag;
	}

	/**
	 * @return string
	 */
	public function getETag()
	{
		return $this->etag;
	}

	/**
	 * @param \DateTime $modificationDate
	 */
	public function setModificationDate(\DateTime $modificationDate)
	{
		$this->modificationDate = $modificationDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getModificationDate()
	{
		return $this->modificationDate;
	}

	/**
	 * @param string $resourceUrl
	 */
	public function setResourceUrl($resourceUrl)
	{
		$this->resourceUrl = $resourceUrl;
		if (null === $this->slug)
		{
			$this->setSlug(pathinfo($resourceUrl, PATHINFO_FILENAME));
		}
	}

	/**
	 * @return string
	 */
	public function getResourceUrl()
	{
		return $this->resourceUrl;
	}

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug($slug)
	{
		$this->slug = $this->tokenize($slug);
	}

	/**
	 * @param string $relativePath
	 */
	public function setRelativePath($relativePath)
	{
		$this->relativePath = trim($relativePath, '/');
	}

	/**
	 * @return string
	 */
	public function getRelativePath()
	{
		return $this->relativePath;
	}



	protected function tokenize($s)
	{
		$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
		$s = strtolower($s);
		$s = strtr($s, '/', '-');
		$s = preg_replace('/[^a-z0-9_\s-]/', '', $s);
		$s = preg_replace('/[\s_-]+/', ' ', $s);
		$s = preg_replace('/[\s_-]/', '-', $s);
		$s = trim($s, '-');
		return $s;
	}


}