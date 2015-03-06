# SimpleOrm
Immature. Do not use.

This is a no-frills simple [ORM](https://en.wikipedia.org/wiki/Object-relational_mapping) class for PHP/sqlite and MySQL. Project goals are to

  - provide ORM functionality
  - be as simple and small as possible
  - be clean.

It is **not** a goal to be compatible with other DBMS than sqlite and MySQL (at the moment) or to implement feature X that
other ORM Y already has.

# Requirements

PHP 5.3 + php-sqlite

# Download

  - [Source can be found and downloaded from Github](https://github.com/rene-s/SimpleOrm)

# Author

Me:

1. https://reneschmidt.de/wiki/index.php/page/view/SimpleOrm,Start
2. https://reneschmidt.de/

# Licence

GPL v3 or commercial licence :) from github@reneschmidt.de. Do not use this in your closed source project
without paying me. I don't like that.

# How to use

First, SimpleOrm supports sqlite and MySQL at the moment. Secondly, SimpleOrm expects every table to have a
numeric PK and it must be given as the first field.

*Set constants*

```php
    // example Sqlite memory database
    define('DB_DSN', 'sqlite::memory:');

    // example Sqlite file database
    define('DB_DSN', 'sqlite:/tmp/db.sqlite');

    // example MySQL database on localhost
    define('DB_DSN', 'mysql:host=localhost;port=3306;dbname=testdb');
    define('DB_DSN', 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb');

    // For MySQL, also define user name and password. Not used for Sqlite.
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
```

*Provide database setup class*

This is not SimpleOrm-specific. You can do this any way you want. I would recommend a similar setup like in this
package: SampleDbConfig. The setUp() method in it creates required tables. Be sure to execute this setup only
when the database does not exist yet.

*Create model class for each table. Example:*

Let's assume you have a table like this:

```sql
   CREATE TABLE sample (
     "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,
     "someName" TEXT NOT null,
     "bitmask" INTEGER NOT null DEFAULT (0)
   );
```

Then create an appropriate model class like this:

```php
   /**
    * Sample Model instance.
    *
    * Define correct type hinting like this:
    *
    * @method Sample findOneBy()
    * @method Sample[] findBy()
    * @method Sample[] findByQuery()
    * @method Sample[] collectRecords()
    */
   class Sample extends SimpleOrm
   {
     /**
      * Array with table fields
      *
      * @var array
      */
     protected $_payload = array(
       "id" => null, // first field is primary key
       "someName" => null,
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

```php
   $sample = new Sample(array("someName" => "abc", "bitmask" => 0));
   $sample->save();

   $sample = Sample::getInst(array("someName" => "abc", "bitmask" => 0));
   $sample->save();

   $sample = new Sample();
   $sample->set("bitmask", 0);
   $sample->set("someName", "abc");
   $sample->save();
```

How to retrieve records:

```php
   $sample = Sample::getInst()->findOneBy("someName", "abc"); // returns record of type "Sample"
   print($sample->get("someName")); // prints "abc"

   $samples = Sample::getInst()->findBy("someName", "abc"); // returns array with "Sample" items

   foreach($samples AS $sample) {
        print($sample->get("someName")); // prints "abc"
   }

   $samples = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE someName = ?", array("abc"));

   foreach($samples AS $sample) {
        print($sample->get("someName")); // prints "abc"
   }
```

How to update and delete records:

```php
   $sample = Sample::getInst()->findOneBy("someName", "abc"); // returns record of type "Sample"
   print($sample->get("someName")); // prints "abc"

   $sample->set("someName", "def");
   $sample->save(); // record now has value "def" for "someName"

   print($sample->get("someName")); // prints "def"

   $sample->del(); // record is deleted now.
```

Full example:

```php
    <?php
    require 'vendor/autoload.php';

    // example MySQL database on localhost
    define('DB_DSN', 'mysql:host=localhost;port=3306;dbname=wordpress');

    // For MySQL, also define user name and password. Not used for Sqlite.
    define('DB_USER', 'username');
    define('DB_PASS', 'password');

    use \SimpleOrm\SimpleOrm;

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
      protected $_payload = array(
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