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
    if (property_exists($this, $what)) {
      return $this->{$what};
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
    if (property_exists($this, $what)) {
      $this->{$what} = $value;
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
    $result = self::findBy($field, $value);

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

    $sth = SimpleDb::getInst()->db->prepare($query);

    $sth->execute(array($value));

    $rawResults = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawResults AS $rawResult) {
      $returnResults[] = new static($rawResult);
    }

    return $returnResults;
  }

  public function save()
  {

  }
}