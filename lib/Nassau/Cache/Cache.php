<?php namespace Nassau\Cache;

class Cache {
	private $_cachePath = 'cache/';
	private $_salt = '';
	private $_ts = 0;
	
	public function __construct($cachePath = '', $salt = '', $ts = 0) {
	  if ($cachePath) $this->_cachePath .= trim($cachePath, '/') . '/';
		if ($salt) $this->_salt = $salt;
		if ($ts) $this->_ts = is_int($ts) ? $ts : strtotime($ts);
	}
	private function getHash($key) {
	  return md5(md5($key) . $this->_salt);
	}
	public function getCachePath($key) {
		$key  = $this->getHash($key);
		$path = sprintf('%s/%s', substr($key, 0, 1), $key);

		return $this->_cachePath . $path . '.cache';
	}
	public function isCacheExpired($key, $ttl = 0) {
		$cPath = $this->getCachePath($key);
		return file_exists($cPath) == false
		  || (filemtime($cPath) < $this->_ts)
		  || ($ttl && (strtotime($ttl, filemtime($cPath)) < time()));
	}
	public function save($key, $data) {
		$path = $this->getCachePath($key);
		if (!file_exists(dirname($path))) mkdir(dirname($path), 0770, true);
		file_put_contents($path, $data);
	}
	public function load($key, $ttl = 0) {
	  if ($this->isCacheExpired($key, $ttl)) return null;
	  
		$cPath = $this->getCachePath($key);
		return file_get_contents($cPath);
	}
}

class ECacheNotFound extends ECacheError {}
class ECacheError extends \Exception {}

