<?php
/**
 * Unit test bootstrap
 *
 * @package    SimpleOrm
 * @subpackage Test
 * @author     Rene Schmidt <github@reneschmidt.de>
 */

define("DB_FILE", ":memory:");
define("DB_DSN", 'sqlite:' . DB_FILE);
//define("DB_DSN", 'sqlite:/tmp/db.sqlite');

require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleDb.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleOrm.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SimpleDbConfig.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SampleDbConfig.class.php';
require_once __DIR__ . '/../../vendor/SimpleOrm/SampleModel.class.php';

