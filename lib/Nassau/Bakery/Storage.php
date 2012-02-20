<?php namespace Nassau\Bakery;

class Storage {

  const SQL_TABLE_EXISTS = 'SELECT count(*) FROM sqlite_master where name in ("posts", "text")';

  const SQL_FIND = 'SELECT * FROM `posts` WHERE name = ?';
  const SQL_FIND_SLUG = 'SELECT count(*) FROM `posts` WHERE link = ?';
  const SQL_FIND_PARSE_QUEUE = 'SELECT name, published FROM `posts` WHERE reparse = 1 AND deleted = 0'; 
  const SQL_INSERT = 'INSERT into `posts` values(:last_index, :name, :etag, :date, NULL, NULL, :link, :published, 0, 1)';
  const SQL_UPDATE = 'UPDATE `posts` SET last_index = :last_index, etag = :etag, modified = :modified, reparse = :reparse, deleted = 0 WHERE name = :name';
  const SQL_UPDATE_DATA = 'UPDATE `posts` SET title = ?, published = ?, reparse = 0 WHERE name = ?';
  const SQL_SET_DELETED_FLAG = 'UPDATE `posts` SET deleted = 1 WHERE last_index != ?';

  const SQL_SELECT_TEXT = 'SELECT value FROM text WHERE name = ?';
  const SQL_INSERT_TEXT = 'INSERT INTO text values(?, ?)';
  const SQL_DELETE_TEXT = 'DELETE FROM text WHERE name = ?';
  
  const DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

  private $db;
  private $indexKey;
  
  public function __construct(\PDO $db) {
    $this->db = $db;
    $this->indexKey = strftime(self::DATE_FORMAT);
    
    if (self::checkStorage($db) == false) {
      throw new StorageNotInitialized;
    }
  }
  
  static public function checkStorage($db) {
    return $db->query(self::SQL_TABLE_EXISTS)->fetchColumn(0) == 2;
  }
  public function getIndexKey() {
    return $this->indexKey;
  }
  public function setIndexKey($key) {
    $this->indexKey = $key;
  }
  public function getByName($name) {
    return $this->fetchOne(self::SQL_FIND, array($name));
  }
  private function date_format($d) {
      return is_int($d) ? strftime(self::DATE_FORMAT, $d) : $d;
  }
  public function create($row) {
    $q = $this->db->prepare(self::SQL_INSERT);
    
    $row = array_merge($row, array (
      'last_index' => $this->indexKey,
      'date' => $this->date_format($row['date']),
    ));
    $q -> execute($row);
  }
  public function update ($name, $row) {
    $q = $this->db->prepare(self::SQL_UPDATE);
    $row = array_merge($row, array (
      'name' => $name,
      'last_index' => $this->indexKey,
    ));
    if (isset($row['modified'])) {
      $row['modified'] = $this->date_format($row['modified']);
    }
    $q -> execute($row);
  }
  
  public function setDeletedFlag() {
    $q = $this->db->prepare(self::SQL_SET_DELETED_FLAG);
    $q->execute(array ($this->indexKey));
  }
  public function hasSlug($slug) {
    $q = $this->db->prepare(self::SQL_FIND_SLUG);
    $q->execute(array ($slug));
    return $q->fetchColumn(0) > 0;
  }
  public function updateData($name, $title, $published) {
    $q = $this->db->prepare(self::SQL_UPDATE_DATA);
    $q -> execute(Array ($title, $published, $name));
  }
  public function getParseQueue() {
    $q = $this->db->query(self::SQL_FIND_PARSE_QUEUE);
    return $q->fetchAll(\PDO::FETCH_ASSOC);
  }
  
  public function saveText($name, $text) {
    $this->db->prepare(self::SQL_DELETE_TEXT)->execute(array($name));
    $q = $this->db->prepare(self::SQL_INSERT_TEXT);
    $q-> execute(array ($name, $text));
  }
  public function getText($name) {
    $q = $this->db->prepare(self::SQL_SELECT_TEXT);
    $q->execute(array ($name));
    
    return $q->fetchColumn(0);
  }
  
  public function fetchOne($SQL, $params = array()) {
    $q = $this->db->prepare($SQL);
    $q->execute((array)$params);
    return $q->fetch(\PDO::FETCH_ASSOC);
  }

}
class StorageNotInitialized extends \Exception {};
