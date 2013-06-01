<?php

namespace Nassau\Bakery;

interface IndexItemInterface
{
	/**
	 * @return \DateTime
	 */
	public function getModificationDate();

	/**
	 * @param \DateTime $date
	 */
	public function setModificationDate(\DateTime $date);

	/**
	 * @return \DateTime
	 */
	public function getCreationDate();

	/**
	 * @param \DateTime $date
	 */
	public function setCreationDate(\DateTime $date);

	/**
	 * @return string
	 */
	public function getResourceUrl();

	/**
	 * @param string $url
	 */
	public function setResourceUrl($url);

	/**
	 * @return string
	 */
	public function getSlug();

	/**
	 * @return string
	 */
	public function getETag();
}