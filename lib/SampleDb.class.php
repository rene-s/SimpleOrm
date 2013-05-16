<?php
/**
 * Sample db
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
class SampleDb implements SimpleDbInterface
{
  /**
   * @var SimpleDb
   */
  public $db;

  /**
   * Constructor
   *
   * @param SimpleDb $simpleDb SimpleDb instance
   */
  public function __construct(SimpleDb $simpleDb)
  {
    $this->db = $simpleDb;
  }

  /**
   * Set up database.
   *
   * @return mixed|void
   */
  public function setUp()
  {
    $this->db->pdo->beginTransaction();
    $this->db->pdo->exec('CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,"someName" TEXT NOT null,"bitmask" INTEGER NOT null DEFAULT (0));');
    $this->db->pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)");
    $this->db->pdo->commit();
  }
}
