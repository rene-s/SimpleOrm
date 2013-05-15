<?php
/**
 * Sample db
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
class SampleDb extends SimpleDb
{
  /**
   * Path to sqlite database
   *
   * @var string
   */
  public static $dbPath = ":memory:"; // "/tmp/.Db.sqlite";

  /**
   * Set up database.
   *
   * @return mixed|void
   */
  protected function setUp()
  {
    $this->db->beginTransaction();
    $this->db->exec('CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,"someName" TEXT NOT null,"bitmask" INTEGER NOT null DEFAULT (0));');
    $this->db->exec("CREATE UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)");
    $this->db->commit();
  }
}
