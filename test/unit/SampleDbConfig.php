<?php
/**
 * SimpleOrm
 *
 * PHP Version 5.5
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt DevOps UG (haftungsbeschränkt) & Co. KG <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
namespace SimpleOrm\Tests;

use SimpleOrm\SimpleDb;
use SimpleOrm\SimpleDbConfig;

/**
 * Sample db config
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt DevOps UG (haftungsbeschränkt) & Co. KG <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
class SampleDbConfig extends SimpleDbConfig
{
    /**
     * @var SimpleDb
     */
    public $simpleDb = null;

    /**
     * Set up database.
     *
     * @return mixed|void
     */
    public function setUp()
    {
        $this->simpleDb->pdo->beginTransaction();
        $this->simpleDb->pdo->exec("DROP " . "TABLE IF EXISTS sample");

        //sqlite
        $this->simpleDb->pdo->exec(
            'CREATE ' . 'TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,' .
            '"someName" TEXT NOT NULL,"bitmask" INTEGER NOT NULL DEFAULT (0));'
        );

        $this->simpleDb->pdo->exec(
            "CREATE " . "UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)"
        );

        //mysql
        /*$this->simpleDb->pdo->exec(
            'CREATE TABLE sample(`id` INTEGER unsigned NOT NULL AUTO_INCREMENT, ' .
            '`someName` TEXT NOT NULL , `bitmask` INTEGER NOT NULL DEFAULT 0, PRIMARY KEY (`id`));'
        );
        $this->simpleDb->pdo->exec(
            "CREATE UNIQUE INDEX uniqueSomeNameIdx ON sample(someName(255))"
        );*/

        $this->simpleDb->pdo->commit();
    }
}
