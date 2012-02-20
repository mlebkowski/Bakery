<?php namespace Nassau\Bakery\Fetcher;

class Dropbox implements FetcherInterface {
  private $dropbox;
  private $auth;
  
  public function __construct($key, $secret, $options = array ()) {
    $this->auth = new \Dropbox_OAuth_PHP($key, $secret);
    $this->dropbox = new \Dropbox_API($this->auth, \Dropbox_API::ROOT_SANDBOX);
    
    $tokens = array_intersect_key($options, array_flip(array("token", "token_secret")));
    if (sizeof($tokens) == 2) {
      $this->auth->setToken($tokens);
    } else if (posix_isatty(STDIN)) {
      $this->authenticate();
    }
  }
  
  public function authenticate() {
    $auth = $this->auth;
    $tokens = $auth->getRequestToken();
    echo "Visit: " . $auth->getAuthorizeUrl(). "\n";
    readline("Hit <return> when done... ");
    $access_token = $auth->getAccessToken();
    echo "Save your access tokens: \n";
    print_r($access_token);
    readline("Hit <return> to continue... ");
  }
  
  public function getMarkdownFiles($path = "/") {
    $files = array();
    $info = $this->dropbox->getMetaData("/");
    if (array_key_exists("contents", $info)) foreach ($info['contents'] as $file) {
      if ($file['is_dir']) {
        $files += $this->getMarkdownFiles($file['path']);  
      } else {
        $path = trim($file['path'], "/");
        $files[$path] = Array (
          'name' => basename($path),
          'path' => "/". $path,
          'date' => strtotime($file['modified']),
          'etag' => $file['rev'],
        );
      }
    }
    return $files;
  }
  
  public function fetch ($path) {
    $path = "/" . ltrim($path, "/");
    return $this->dropbox->getFile($path);
  }  
  
  public function index ($url = "/") {
    $url = "/" . trim($url, "/");
    try {
      $list = $this -> getMarkdownFiles($url);
    } catch (\Dropbox_Exception_Forbidden $E) {
    
    } catch (\OAuthException $E) {
    
    }
    return $list;
  }
}

