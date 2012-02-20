<?php namespace Nassau\Bakery;

class Indexer {
  private $db;
  private $fetcher;
  
  const SQL_FIND = "SELECT * FROM `posts` WHERE name = ?";
  
  public function __construct(Storage $db) {
    $this->db = $db;
  }
  
  public function processIndex(array $index, array $options = array()) {
    $options = $options + Array (
      'auto_publish' => true,
      'parser_timestamp' => 0, // cache
      'index_key' => strftime('%Y-%m-%d %H:%M:%S'),
    );
    
    $indexKey = $options['index_key'];
    $db = $this->db;
    $db->setIndexKey($indexKey);

    foreach ($index as $name => $item) {
      $row = $db->getByName($name);
      if (!$row) {
        $row = array (
          'name' => $name,
          'date' => $item['date'] ?: time(),
          'link' => $this->createSlug($name),
          'published' => $options['auto_publish'],
          'etag' => $item['etag'],
        );
        $db->create($row);
      } else {
        $reparse = $options['parser_timestamp'] > strtotime($row['last_index']);
        $db->update($name, array (
          'reparse' => $reparse || $row['reparse'] || ($item['etag'] != $row['etag']),
          'etag' => $item['etag'],
          'modified' => isset($item['modified']) ? $item['modified']: strtotime($row['date']),
        ));
      }
    }
    $db->setDeletedFlag();
  }
  
  public function createSlug($name) {
    $pathinfo = pathinfo($name);
    $tokenizer = function ($s) {
      $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
      $s = strtolower($s);
      $s = strtr($s, '/', '-');
      $s = preg_replace('/[^a-z0-9_\s-]/', '', $s);
      $s = preg_replace('/[\s_-]+/', ' ', $s);
      $s = preg_replace('/[\s_-]/', '-', $s);
      $s = trim($s, '-');
      return $s;
    };
    
    $name = $pathinfo['filename'];
    $dirParts = explode("/", $pathinfo['dirname']);
    $i = 1;
    do {
      $slug = $tokenizer($name);
      if (! $this->db->hasSlug($slug)) {
        return $slug;
      }
      if (sizeof($dirParts)) {
        $name = sprintf('%s/%s', array_pop($dirParts), $name);
      } else {
        $name = sprintf('%s-%d', $pathinfo['filename'], $i++);
      }
    } while ($i < 10);
    return null;
  }
}
