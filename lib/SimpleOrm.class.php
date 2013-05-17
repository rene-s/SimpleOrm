<?php
/**
 * Simple ORM
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
abstract class SimpleOrm
{
  /**
   * Array with table fields
   *
   * @var array
   */
  protected $_payload = array();

  /**
   * @var string
   */
  protected static $table = "set_table_in_model_derived_from_this_class";

  /**
   * Constructor
   *
   * @param array $data Array with data to be set
   */
  public function __construct(array $data = array())
  {
    if (!empty($data)) {
      $this->fromArray($data);
    }
  }

  /**
   * Getter
   *
   * @param string     $what    Name of property to get
   * @param mixed|null $default Default value in case property does not exist
   *
   * @return mixed|null
   */
  public function get($what, $default = null)
  {
    if (array_key_exists($what, $this->_payload)) {
      return $this->_payload[$what];
    }

    return $default;
  }

  /**
   * Setter
   *
   * @param string $what  Name of property to get
   * @param mixed  $value Value to be set
   *
   * @return void
   */
  public function set($what, $value)
  {
    if (array_key_exists($what, $this->_payload)) {
      $this->_payload[$what] = $value;
    }
  }

  /**
   * Populate properties from array
   *
   * @param array $data Array with data to be set
   *
   * @return void
   */
  public function fromArray(array $data)
  {
    foreach ($data AS $field => $value) {
      $this->set($field, $value);
    }
  }

  /**
   * Find one record
   *
   * @param string $field Field name
   * @param mixed  $value Value
   *
   * @return null|SimpleOrm
   * @static
   */
  public static function findOneBy($field, $value)
  {
    $result = static::findBy($field, $value);

    if (empty($result)) {
      return null;
    }

    return $result[0];
  }

  /**
   * Find records
   *
   * @param string $field Field name
   * @param mixed  $value Value
   *
   * @return array
   * @static
   */
  public static function findBy($field, $value)
  {
    $returnResults = array();
    $query = "SELECT * FROM " . static::$table . " WHERE " . $field . " = ?";

    $pdo = SimpleDb::getInst()->pdo;

    $sth = $pdo->prepare($query);

    $sth->execute(array($value));

    $rawResults = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawResults AS $rawResult) {
      $returnResults[] = new static($rawResult);
    }

    return $returnResults;
  }

  /**
   * Insert record
   *
   * @return int Last record ID
   */
  public function insert()
  {
    $sql = 'INSERT INTO %s (%s) VALUES (%s)';

    $placeholders = array_fill(0, count($this->_payload), '?');

    return $this->execute(
      sprintf($sql, static::$table, implode(",", array_keys($this->_payload)), implode(",", $placeholders))
    );
  }

  /**
   * Update record
   *
   * @return int Last record ID
   */
  public function update()
  {
    $sql = 'UPDATE %s SET %s WHERE id = %d'; // only numeric PKs are supported and their name must be "id".

    $placeholders = array_keys($this->_payload);

    foreach ($placeholders AS $k => $val) {
      $placeholders[$k] = sprintf("%s = ?", $val);
    }

    return $this->execute(sprintf($sql, static::$table, implode(",", $placeholders), $this->get("id")));
  }

  /**
   * Execute SQL query
   *
   * @param string $sql SQL query
   *
   * @return int Last record ID
   */
  public function execute($sql)
  {
    $pdo = SimpleDb::getInst()->pdo;
    $sth = $pdo->prepare($sql);

    $sth->execute(array_values($this->_payload));

    return $pdo->lastInsertId();
  }

  /**
   * Save record
   *
   * @return int Last record ID
   */
  public function save()
  {
    if ($this->get("id")) {
      return $this->update();
    }

    return $this->insert();
  }
}