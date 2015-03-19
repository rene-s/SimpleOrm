<?php

namespace SimpleOrm\Tests;

use SimpleOrm\SimpleDb;
use SimpleOrm\SimpleDbConfig;

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

        //sqlite
        $this->simpleDb->pdo->exec('CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,"someName" TEXT NOT NULL,"bitmask" INTEGER NOT NULL DEFAULT (0));');
        $this->simpleDb->pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)");

        //mysql
        //$this->simpleDb->pdo->exec('CREATE TABLE sample(`id` INTEGER unsigned NOT NULL AUTO_INCREMENT, `someName` TEXT NOT NULL , `bitmask` INTEGER NOT NULL DEFAULT 0, PRIMARY KEY (`id`));');
        //$this->simpleDb->pdo->exec("CREATE UNIQUE INDEX uniqueSomeNameIdx ON sample(someName(255))");

        $this->simpleDb->pdo->commit();
    }
}
