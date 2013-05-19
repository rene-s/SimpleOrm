# SimpleOrm
Immature. Do not use.

This is just a no-frills simple ORM class for PHP/sqlite. Project goals are to

  - be as simple and small as possible
  - be clean.

It is **not** a goal to be compatible with other DBMS than sqlite (at the moment) or to implement feature X that
other ORM Y already has.

# Author

Me:

1. https://www.reneschmidt.de/wiki/index.php/view/SimpleOrm/Start
2. https://www.reneschmidt.de/

# Licence

GPL v3 or commercial licence :) from github@reneschmidt.de. Do not use this in your closed source project
without paying me. I don't like that.

# How to use

First, SimpleOrm only supports sqlite at the moment. Secondly, SimpleOrm expects every table to have a
numeric PK named "id".

1. Set two constants:

       define("DB_FILE", ":memory:"); // or path to sqlite file: define("DB_FILE", "/path/to/database.sqlite");
       define("DB_DSN", 'sqlite:' . DB_FILE);

2. Provide database setup class.

   This is not SimpleOrm-specific. You can do this any way you want. I would recommend a similar setup like in this
   package: SampleDbConfig. The setUp() method in it creates required tables. Be sure to execute this setup only
   when the database does not exist yet.

3. Create model class for each table. Example:

   Let's assume you have a table like this:

       CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,"someName" TEXT NOT null,"bitmask" INTEGER NOT null DEFAULT (0));

   Then create an appropriate model class like this:

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
           "id" => null,
           "someName" => null,
           "bitmask" => null
         );

         /**
          * @var string
          */
         protected static $table = 'sample';
       }

   That's it.

4. How to use.

   There are different methods of creating new records:

       $sample = new Sample(array("someName" => "abc", "bitmask" => 0));
       $sample->save();

       $sample = Sample::getInst(array("someName" => "abc", "bitmask" => 0));
       $sample->save();

       $sample = new Sample();
       $sample->set("bitmask", 0);
       $sample->set("someName", "abc");
       $sample->save();

   How to retrieve records:

       $sample = Sample::getInst()->findOneBy("someName", "abc"); // returns record of type "Sample"
       print($sample->get("someName")); // prints "abc"

       $samples = Sample::getInst()->findBy("someName", "abc")); // returns array with "Sample" items

       foreach($samples AS $sample) {
            print($sample->get("someName")); // prints "abc"
       }

       $samples = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE someName = ?", array("abc"));

       foreach($samples AS $sample) {
            print($sample->get("someName")); // prints "abc"
       }

    How to update and delete records:

       $sample = Sample::getInst()->findOneBy("someName", "abc"); // returns record of type "Sample"
       print($sample->get("someName")); // prints "abc"

       $sample->set("someName", "def");
       $sample->save(); // record now has value "def" for "someName"

       print($sample->get("someName")); // prints "def"

       $sample->del(); // record is deleted now.

Have fun.