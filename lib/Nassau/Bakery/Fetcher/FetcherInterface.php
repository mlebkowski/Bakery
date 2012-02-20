<?php namespace Nassau\Bakery\Fetcher;

interface FetcherInterface {
  public function __construct ($user, $pass, $options = array ());
  public function index($url = "/");
  public function fetch($path);

}
