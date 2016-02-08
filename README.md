# SimpleOrm
Immature. Do not use.

This is a no-frills simple [ORM](https://en.wikipedia.org/wiki/Object-relational_mapping) class for PHP/sqlite and MySQL. Project goals are to

  - provide ORM functionality
  - be as simple and small as possible
  - be clean.

It is **not** a goal to be compatible with other DBMS than sqlite and MySQL (at the moment) or to implement feature X that
other ORM Y already has. It might not even fit into the traditional ORM paradigm.

## German Web Application Developer Available for Hire!

No marketing skills whatsoever, but low rates, nearly 20 years of experience, and german work attitude.

Get in touch now: https://www.reneschmidt.de/DevOps/#contact

[![Build Status](https://travis-ci.org/rene-s/SimpleOrm.svg)](https://travis-ci.org/rene-s/SimpleOrm)
[![License](https://img.shields.io/badge/License-LGPL-blue.svg)](https://opensource.org/licenses/LGPL-3.0)

# Requirements

PHP 5.3 + php-sqlite

# Download

  - [Source can be found and downloaded from Github](https://github.com/rene-s/SimpleOrm)

# Author

Me:

1. https://reneschmidt.de/wiki/index.php/page/view/SimpleOrm,Start
2. https://reneschmidt.de/

# Licence

GPL v3 or commercial licence :) from rene+_gth@reneschmidt.de. Do not use this in your closed source project
without paying me. I don't like that.

# How to use

First, SimpleOrm supports sqlite and MySQL at the moment. Secondly, SimpleOrm expects every table to have a
numeric PK and it must be given as the first field.

*Set variables*

```
// example Sqlite memory database
$dsn = 'sqlite::memory:';

// OR: example Sqlite file database
$dsn = 'sqlite:/tmp/db.sqlite';

// OR: example MySQL database on localhost
$dsn = 'mysql:host=localhost;port=3306;dbname=testdb';
$dsn = 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb';

// For MySQL, also define user name and password. **NOT** used for Sqlite.
$user = 'root';
$pass = 'root';

// Set up DB connection
$simpleDb = SimpleDb::getInst($dsn, $user, $pass);

// You need to provide your own implementation of SimpleDbConfig (here we use SampleDbConfig)
$sampleDbConfig = SampleDbConfig::getInst($simpleDb);

// Setup will create the database and the tables according to your SampleDbConfig implementation
// Obviously you want this to execute only during installation of the app.
$sampleDbConfig->setUp();
```

*Provide database setup class*

This is not SimpleOrm-specific. You can do this any way you want. I would recommend a similar setup like in this
package: SampleDbConfig. The setUp() method in it creates required tables. Be sure to execute this setup only
when the database does not exist yet.

*Create model class for each table. Example:*

Let's assume you have a table like this:

```
CREATE TABLE sample (
 "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,
 "some_name" TEXT NOT null,
 "bitmask" INTEGER NOT null DEFAULT (0)
);
```

Then create an appropriate model class like this:

```
/**
 * Sample Model instance.
 *
 * Define correct type hinting like this:
 *
 * @method Sample findOneBy($field, $value, $fetchMode = \PDO::FETCH_OBJ)
 * @method Sample[] findBy($field, $value, $fetchMode = \PDO::FETCH_OBJ)
 * @method Sample[] findByQuery($query, array $values, $fetchMode = \PDO::FETCH_OBJ)
 * @method Sample[] collectRecords(\PDOStatement $sth, $fetchMode = \PDO::FETCH_OBJ)
 */
class Sample extends SimpleOrm
{
 /**
  * Array with table fields
  *
  * @var array
  */
 protected $payload = array(
   "id" => null, // first field is primary key
   "some_name" => null,
   "bitmask" => null
 );

 /**
  * @var string
  */
 protected static $table = 'sample';
}
```

That's it.

*How to use*

There are different methods of creating new records:

```
$sample = new Sample(array("some_name" => "abc", "bitmask" => 0));
$sample->save();

$sample = Sample::getInst(array("some_name" => "abc", "bitmask" => 0));
$sample->save();

$sample = new Sample();
$sample->set("bitmask", 0);
$sample->set("some_name", "abc");
$sample->save();
```

How to retrieve records:

```
$sample = Sample::getInst()->findOneBy("some_name", "abc"); // returns record of type "Sample"
print($sample->get("some_name")); // prints "abc"

$samples = Sample::getInst()->findBy("some_name", "abc"); // returns array with "Sample" items
$samples = Sample::getInst()->findBy("some_name", "abc", \PDO\FETCH_ASSOC); // returns array with "Sample" array

foreach($samples AS $sample) {
    print($sample->get("some_name")); // prints "abc"
}

$samples = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE some_name = ?", ["abc"]);

foreach($samples AS $sample) {
    print($sample->get("some_name")); // prints "abc"
}

// apply filter
$samples = Sample::getInst()->setFilter(function($inst) {
    $inst['some_name'] = $inst['some_name'] . 'x'; // apply filter to array
    return $inst;
})->findByQuery("SELECT * FROM sample WHERE some_name = ?", ["abc"]);

foreach($samples AS $sample) {
    print($sample->get("some_name")); // prints "abcx"
}
```

How to update and delete records:

```
$sample = Sample::getInst()->findOneBy("some_name", "abc"); // returns record of type "Sample"
print($sample->get("some_name")); // prints "abc"

$sample->set("some_name", "def");
$sample->save(); // record now has value "def" for "some_name"

print($sample->get("some_name")); // prints "def"

$sample->del(); // record is deleted now.
```

Full example:

```
<?php
require 'vendor/autoload.php';

// example MySQL database on localhost
$dsn = 'mysql:host=localhost;port=3306;dbname=wordpress';

// For MySQL, also define user name and password. Not used for Sqlite.
$user = 'username';
$pass = 'password';

use \SimpleOrm\SimpleOrm;

$simpleDb = SimpleDb::getInst($dsn, $user, $pass);

// You need to provide your own implementation of SimpleDbConfig (here we use WpDbConfig)
$wpDbConfig = WpDbConfig::getInst($simpleDb);

// Setup will create the database and the tables according to your SampleDbConfig implementation
// Obviously you want this to execute only during installation of the app.
$wpDbConfig->setUp();

/**
 * WpUserMeta Model instance.
 *
 * Define correct type hinting like this:
 *
 * @method WpUserMeta findOneBy()
 * @method WpUserMeta[] findBy()
 * @method WpUserMeta[] findByQuery()
 * @method WpUserMeta[] collectRecords()
 */
class WpUserMeta extends SimpleOrm
{
  /**
   * Array with table fields
   *
   * @var array
   */
  protected $payload = array(
    'umeta_id' => null,
    'user_id' => null,
    'meta_key' => null,
    'meta_value' => null
  );

  /**
   * @var string
   */
  protected static $table = 'wp_usermeta';
}

$user_metas = WpUserMeta::getInst()->findBy("meta_key", "description"); // returns array with "WpUserMeta" items

foreach ($user_metas AS $user_meta) {
  print_r($user_meta->toArray());
}
```

Have fun.