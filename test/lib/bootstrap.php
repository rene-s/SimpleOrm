<?php
/**
 * Unit test bootstrap
 *
 * @package    SimpleOrm
 * @subpackage Test
 * @author     Rene Schmidt <github@reneschmidt.de>
 */

// example Sqlite memory database
define('DB_DSN', 'sqlite::memory:');

// example Sqlite file database
//define('DB_DSN', 'sqlite:/tmp/db.sqlite');

// example MySQL database on localhost
//define('DB_DSN', 'mysql:host=localhost;port=3306;dbname=testdb');
//define('DB_DSN', 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb');

// For MySQL, also define user name and password. Not used for Sqlite.
define('DB_USER', 'root');
define('DB_PASS', 'root');

require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleDb.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleOrm.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleDbConfig.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SampleDbConfig.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SampleModel.class.php';

