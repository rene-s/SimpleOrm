<?php

namespace SimpleOrm;

/**
 * Sample db config
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
class SampleDbConfig extends SimpleDbConfig
{
  /**
   * @var SimpleDb
   */
  public $simpleDb = null;

  /**
   * Constructor
   *
   * @param SimpleDb $simpleDb SimpleDb instance
   */
  public function __construct(SimpleDb $simpleDb)
  {
    $this->simpleDb = $simpleDb;
  }

  /**
   * Set up database.
   *
   * @return mixed|void
   */
  public function setUp()
  {
    $this->simpleDb->pdo->beginTransaction();
    $this->simpleDb->pdo->exec("DROP TABLE IF EXISTS sample");
    $this->simpleDb->pdo->exec('CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,"someName" TEXT NOT null,"bitmask" INTEGER NOT null DEFAULT (0));');
    $this->simpleDb->pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)");
    $this->simpleDb->pdo->commit();
  }
}
