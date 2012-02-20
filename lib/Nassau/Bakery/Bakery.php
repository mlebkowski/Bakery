<?php namespace Nassau\Bakery;

class Bakery {
  private $config; 
  
  public function __construct(\Nassau\Config\Config $config) {
    $this->config = $config;
    $projects = $config->read('Projects');
    foreach ($projects as $project => $settings):
      $parsed = parse_url($settings['pull']);
      if (empty($parsed['scheme'])) $parsed['scheme'] = '';
      
      $fetcher = $this->getFetcher($parsed['scheme'], $parsed);
      
      $db = new \PDO(sprintf('sqlite:data/%s.db', $project));
      if (! Storage::checkStorage($db)) {
        foreach ($config->read('Sql/Tables') as $table => $columns) {
          $cols = array();
          foreach ($columns as $entry) {
            list ($col, $type) = explode(" ", trim($entry));
            $cols[] = sprintf("%s %s", $col, $type);
          }
          $db->query(sprintf('CREATE TABLE `%s` (%s)', $table, implode(',', $cols)));
        }
      }
      $storage = new Storage($db);
      $indexer = new Indexer($storage);
      
      $index_data = $fetcher->index($parsed['path']);

      $cache = $this->getCache('misc');
      $index_hash = md5(json_encode($index_data));
      $cache_hash = $cache->load('index.last');
      if ($cache_hash == $index_hash) return;
      $cache->save('index.last', $index_hash);


      $indexer->processIndex($index_data);
      
      $queue = $storage -> getParseQueue();
      if (empty($queue)) return;
      
      // parser moze zalezec od ustawien!
      $md = $this->getMarkdown();
      foreach ($queue as $item) {
        $data = $fetcher->fetch ($item['name']);
        $text  = $md->parse($data);
        $title = $md->getTitle($data);
        $storage->updateData($item['name'], $title, $item['published']);
        $storage->saveText($item['name'], $text);
      }
      
      // parser -> oddzielic naglowek od tresci
      
      // dodac:
      // pominiecie cache
      // refresh cache przy zmianie:
      //  - silnika md
      //  - szablonu
      //  - ustawien
      
      // $oven->bake();
      // $ups->push();
      
    endforeach;
  }
  
  private $_markdown = null;
  public function getMarkdown() {
    if (!$this->_markdown):
      $oEmbedProviders = \Symfony\Component\Yaml\Yaml::parse('etc/oembed.yaml');
      $oembed = new \Markdown\oEmbed\oEmbed;
      foreach ($oEmbedProviders as $provider) {
        $oembed->addProvider(new \Markdown\oEmbed\Provider\Provider($provider['Endpoint'], $provider['Schemes']));
      }
      $me = new \Markdown\Embed($oembed);
      $ca = $this->getCache('markdown');
      $this->_markdown = new \Markdown\Markdown($ca, $me);
    endif;

    return $this->_markdown;
  }
  private $caches = array();
  public function getCache($name) {
    if (empty($this->caches[$name])) {
      $expire = $this->config->read('Env/date', 0);
      $cache = new \Nassau\Cache\Cache($name, null, $expire);
      $this->caches[$name] = $cache;
    }
    return $this->caches[$name];
  }
  public function getFetcher($type, $options = array()) {
    switch (strtolower($type)): 
    case 'dropbox':
      $key     = $this->config->read('Dropbox/key');
      $secret  = $this->config->read('Dropbox/secret');
      $fetcher = new Fetcher\Dropbox($key, $secret, array(
          'token' => $options['user'],
          'token_secret' => $options['pass'],
      ));
      break;
    default:
      // filesystem
    endswitch;
    return $fetcher;
  }

}
