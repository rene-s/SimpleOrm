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

require_once __DIR__ . '/../../lib/SimpleDb.class.php';
require_once __DIR__ . '/../../lib/SimpleOrm.class.php';
require_once __DIR__ . '/../../lib/SimpleDbConfig.class.php';
require_once __DIR__ . '/../../lib/SampleDbConfig.class.php';
require_once __DIR__ . '/../../lib/SampleModel.class.php';
