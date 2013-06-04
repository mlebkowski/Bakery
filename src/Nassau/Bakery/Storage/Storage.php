<?php

namespace Nassau\Bakery\Storage;

class Storage
{

	const SQL_FIND = 'SELECT * FROM `post` WHERE slug = ?';
	const SQL_FIND_SLUG = 'SELECT count(1) FROM `post` WHERE slug = ?';
	const SQL_FIND_PARSE_QUEUE = 'SELECT slug FROM `post` WHERE reparse = 1 AND deleted = 0';
	const SQL_INSERT = 'INSERT into `post` VALUES (:slug, :category, :etag, :date, :modified, NULL, :link, 0, 1, DATETIME("now"))';
	const SQL_UPDATE = 'UPDATE `post` SET etag = :etag, modified = :modified, reparse = CASE WHEN (reparse OR etag != :etag) THEN 1 ELSE :reparse END, deleted = 0, last_indexed = DATETIME("now") WHERE slug = :slug';
	const SQL_UPDATE_POST_TITLE = 'UPDATE `post` SET title = :title, reparse = 0 WHERE slug = :slug';
	const SQL_SET_DELETED_FLAG = 'UPDATE `post` SET deleted = 1 WHERE last_indexed < DATETIME("now", "-10 minutes")';

	const SQL_SELECT_TEXT = 'SELECT value FROM text WHERE slug = ?';
	const SQL_INSERT_TEXT = 'INSERT INTO text VALUES (?, ?, DATETIME("now"))';
	const SQL_DELETE_TEXT = 'DELETE FROM text WHERE slug = ?';

	const SQL_HASH_FIND = 'SELECT value FROM hash WHERE name = ?';
	const SQL_HASH_CLEAR = 'DELETE FROM hash WHERE name = ?';
	const SQL_HASH_INSERT = 'INSERT INTO hash VALUES (?, ?, DATETIME("now"))';

	const DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * @var \PDO
	 */
	protected $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

	public function initTable(TableDefinitionInterface $table)
	{
		return $this->db->query($table->toSql());
	}

	public function getBySlug($slug)
	{
		return $this->fetchOne(self::SQL_FIND, array($slug));
	}

	public function create($slug, $category, $etag, \DateTime $date, \DateTime $modified)
	{
		$this->db->prepare(self::SQL_INSERT)->execute(array (
			'slug' => $slug,
			'category' => $category,
			'etag' => $etag,
			'date' => $date->format(self::DATE_FORMAT),
			'modified' => $modified->format(self::DATE_FORMAT)
		));
	}

	public function update($slug, $etag, \DateTime $modified, $reparse)
	{
		$this->db->prepare(self::SQL_UPDATE)->execute(array (
			'slug' => $slug,
			'etag' => $etag,
			'modified' => $modified->format(self::DATE_FORMAT),
			'reparse' => (int) (bool) $reparse,
		));
	}

	public function setDeletedFlag()
	{
		$this->db->prepare(self::SQL_SET_DELETED_FLAG)->execute();
	}

	public function updatePostTitle($slug, $title)
	{
		$this->db->prepare(self::SQL_UPDATE_POST_TITLE)->execute(array (
			'title' => $title,
			'slug' => $slug,
		));
	}

	public function getParseQueue()
	{
		return $this->db->query(self::SQL_FIND_PARSE_QUEUE)->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function saveText($slug, $text)
	{
		$this->db->prepare(self::SQL_DELETE_TEXT)->execute(array($slug));
		$this->db->prepare(self::SQL_INSERT_TEXT)->execute(array($slug, $text));
	}

	public function getText($slug)
	{
		$q = $this->db->prepare(self::SQL_SELECT_TEXT);
		$q->execute(array($slug));

		return $q->fetchColumn(0);
	}

	public function setHash($name, $value)
	{
		$this->db->prepare(self::SQL_HASH_CLEAR)->execute(array($name));
		$this->db->prepare(self::SQL_HASH_INSERT)->execute(array ($name, $value));
	}
	public function getHash($name)
	{
		$q = $this->db->prepare(self::SQL_HASH_FIND);
		$q->execute(array ($name));

		return $q->fetchColumn(0);
	}
	protected function fetchOne($SQL, $params = array())
	{
		$q = $this->db->prepare($SQL);
		$q->execute((array)$params);
		return $q->fetch(\PDO::FETCH_ASSOC);
	}

}
