<?php
/**
 * Unit test bootstrap
 *
 * @package    SimpleOrm
 * @subpackage Test
 * @author     Rene Schmidt <github@reneschmidt.de>
 */

// example Sqlite memory database
$dsn = 'sqlite::memory:';

// example Sqlite file database
//$dsn = 'sqlite:/tmp/db.sqlite');

// example MySQL database on localhost
//$dsn = 'mysql:host=localhost;port=3306;dbname=testdb');
//$dsn = 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb');

// For MySQL, also define user name and password. Not used for Sqlite.
$user = 'root';
$pass = 'root';

$autoloader = require 'vendor/autoload.php';