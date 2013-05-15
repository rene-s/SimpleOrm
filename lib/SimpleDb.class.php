<?php
/**
 * Db class. Geared towards sqlite.
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
abstract class SimpleDb
{
  /**
   * @var string
   */
  public static $dbPath = "";

  /**
   * Instance container
   *
   * @var SimpleDb
   */
  private static $instance = null;

  /**
   * DB connection
   *
   * @var PDO
   */
  public $db = null;

  /**
   * Do not use
   */
  private function __construct()
  {
    $this->createDbConn();
  }

  /**
   * Destructor
   */
  public function __destruct()
  {
    self::$instance = null;
  }

  /**
   * Create RuntimeData instance
   *
   * @static
   * @return SimpleDb
   */
  public static function getInst()
  {
    if (null === self::$instance) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  /**
   * Disable cloning
   *
   * @return void
   * @throws Exception
   */
  public function __clone()
  {
    throw new Exception("Cloning is forbidden", 10010);
  }

  /**
   * Set up database
   *
   * @abstract
   * @return mixed
   */
  abstract protected function setUp();

  /**
   * Create DB conn.
   *
   * Creates tables if necessary
   *
   * @return void
   * @throws Exception
   */
  public function createDbConn()
  {
    if (empty(static::$dbPath)) {
      throw new Exception("dbPath cannot be empty.");
    }

    $dir = dirname(static::$dbPath);

    if (!file_exists($dir)) {
      mkdir($dir);
    }

    $setUpDatabase = !file_exists(static::$dbPath) || filesize(static::$dbPath) < 1;

    $this->db = new PDO('sqlite:' . static::$dbPath);

    if ($setUpDatabase) {
      $this->setUp();
    }
  }
}