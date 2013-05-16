<?php
/**
 * Unit test bootstrap
 *
 * @package    SimpleOrm
 * @subpackage Test
 * @author     Rene Schmidt <github@reneschmidt.de>
 */

//define("DB_DSN", 'sqlite::memory:');
define("DB_DSN", 'sqlite:/tmp/db.sqlite');
define("DB_MANAGER", 'SampleDb');

require_once __DIR__ . '/../../lib/SimpleDb.class.php';
require_once __DIR__ . '/../../lib/SimpleOrm.class.php';
require_once __DIR__ . '/../../lib/SampleDb.class.php';
require_once __DIR__ . '/../../lib/SampleModel.class.php';
