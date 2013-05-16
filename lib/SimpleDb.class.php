<?php
/**
 * Interface for db manager
 */
interface SimpleDbInterface
{
  public function setUp();
}

/**
 * Db class. Geared towards sqlite.
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
class SimpleDb
{
  /**
   * Instance container
   *
   * @var SimpleDb
   */
  protected static $instance = null;

  /**
   * DB connection
   *
   * @var PDO
   */
  public $pdo = null;

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
      self::$instance = new self();
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
   * Create DB conn.
   *
   * Creates tables if necessary
   *
   * @return void
   * @throws Exception
   */
  public function createDbConn()
  {
    preg_match("/^(sqlite|mysql):(.*)$/", DB_DSN, $matches);

    $dbPath = $matches[2];

    //$dir = dirname($dbPath);

    /*if (!file_exists($dir) && $dbPath !== ":memory:") {
      mkdir($dir);
    }

    $setUpDatabase = !file_exists($dbPath) || filesize($dbPath) < 1;
*/
    $this->pdo = new PDO(DB_DSN);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
    if ($setUpDatabase) {
      $className = DB_MANAGER;
      self::$manager = new $className($this);
      self::$manager->setUp();
    }*/
  }
}