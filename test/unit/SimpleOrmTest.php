<?php

use SimpleOrm\SimpleOrm;
use SimpleOrm\SimpleDb;
use SimpleOrm\SimpleDbConfig;

/**
 * SimpleOrm test
 *
 * @package    SimpleOrm
 * @subpackage TestUnit
 * @author     Rene Schmidt <rene@reneschmidt.de>
 */
class SimpleOrmTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var bool
   */
  protected $isMySql = false;

  /**
   * Set setup
   *
   * @return void
   */
  public function setUp()
  {
    $className = null;

    if (mt_rand(0, 9) > 5) {
      $className = "SampleDbConfig"; // test *not* using late static binding
    }

    $simpleDb = SimpleDb::getInst();

    $sampleDbConfig = SampleDbConfig::getInst($simpleDb, $className);
    $sampleDbConfig->setUp();

    $this->isMySql = preg_match("/^mysql:/", DB_DSN) > 0;
  }

  /**
   * Create record
   *
   * @param array $data Record data
   *
   * @return void
   */
  protected function createRecord(array $data)
  {
    $pdo = SimpleDb::getInst()->pdo;

    $pdo->exec(
      "INSERT INTO sample ("
      . implode(",", array_keys($data)) . ") VALUES ('"
      . implode("','", array_values($data)) . "')"
    );
  }

  /**
   * Test create db connection
   *
   * @return void
   */
  public function testCreateDbConn()
  {
    $pdo = SimpleDb::getInst()->pdo;

    $this->assertInstanceOf("PDO", $pdo);
  }

  /**
   * Test findBy
   *
   * @return void
   */
  public function testFindBy()
  {
    $this->createRecord(array("id" => 1, "someName" => "/Sample/Two"));

    $samples = Sample::getInst()->findBy("someName", "/Sample/Two");

    $this->assertInternalType("array", $samples);
    $this->assertSame(1, count($samples));
    $this->assertInstanceOf("Sample", $samples[0]);
    $this->assertSame("/Sample/Two", $samples[0]->get("someName"));
  }

  /**
   * Test findOneBy
   *
   * @return void
   */
  public function testFindOneBy()
  {
    $this->createRecord(array("id" => 1, "someName" => "/Sample/One"));

    $sample = Sample::getInst()->findOneBy("someName", "/Sample/One");

    $this->assertInstanceOf("Sample", $sample);
    $this->assertSame("/Sample/One", $sample->get("someName"));
  }

  /**
   * Test insert
   *
   * @return void
   */
  public function testInsert()
  {
    $newSample = new Sample();
    $newSample->set("someName", "/Aye/Bee/Cee");
    $newSample->set("bitmask", "1");

    $id = $newSample->save();
    $this->assertTrue($id > 0);

    $sample = Sample::getInst()->findOneBy("someName", "/Aye/Bee/Cee");

    $this->assertInstanceOf("Sample", $sample);
    $this->assertSame("/Aye/Bee/Cee", $sample->get("someName"));
    $this->assertSame($id, $sample->get("id"));
  }

  /**
   * Test update
   *
   * @return void
   */
  public function testUpdate()
  {
    $this->createRecord(array("id" => 1, "someName" => "/Sample/One"));

    $sample = Sample::getInst()->findOneBy("someName", "/Sample/One");

    $this->assertInstanceOf("Sample", $sample);
    $this->assertSame("/Sample/One", $sample->get("someName"));
    $this->assertTrue($sample->get("id") > 0);

    $sample->set("someName", "/Sample/Six");
    $id = $sample->save();

    $newSample = Sample::getInst()->findOneBy("someName", "/Sample/Six");

    if ($this->isMySql) {
      $this->assertNotSame($sample->get("id"), $id);
      $this->assertNotSame($id, $newSample->get("id"));
    } else {
      $this->assertSame($sample->get("id"), $id);
      $this->assertSame($id, $newSample->get("id"));
    }
  }

  /**
   * Test findByQuery
   *
   * @return void
   */
  public function testFindByQuery()
  {
    $this->createRecord(array("id" => 1, "someName" => "/Sample/One"));

    $samples = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE id = ?", array(1));

    $this->assertInternalType("array", $samples);
    $this->assertSame(1, count($samples));
    $this->assertEquals(1, $samples[0]->get("id"));

    $samples = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE id > ?", array(1));

    $this->assertInternalType("array", $samples);
    $this->assertEmpty($samples);
  }

  /**
   * Test delete
   *
   * @return void
   */
  public function testDelete()
  {
    $newSample = new Sample();
    $newSample->set("someName", "/Aye/Bee/Cee");
    $newSample->set("bitmask", "1");

    $id = $newSample->save();
    $this->assertTrue($id > 0);

    $newSample->del();

    $this->assertNull($newSample->get("id"));

    $sample = Sample::getInst()->findOneBy("someName", "/Aye/Bee/Cee");
    $this->assertNull($sample);

    $samples = Sample::getInst()->findBy("someName", "/Aye/Bee/Cee");
    $this->assertInternalType("array", $samples);
    $this->assertEmpty($samples);
  }

  /**
   * Test fromArray()
   *
   * @return void
   */
  public function testFromArray()
  {
    $sampleOne = new Sample(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 64));
    $this->assertSame(64, $sampleOne->get("bitmask"));

    $sampleTwo = Sample::getInst(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 128));
    $this->assertSame(128, $sampleTwo->get("bitmask"));

    $sampleThree = new Sample();
    $sampleThree->fromArray(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 256));
    $this->assertSame(256, $sampleThree->get("bitmask"));
  }

  /**
   * Test fromArray(), same as above but also give model class name as if using PHP 5.2
   *
   * @return void
   */
  public function testFromArrayOld()
  {
    $sampleOne = new Sample(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 64), 'Sample');
    $this->assertSame(64, $sampleOne->get("bitmask"));

    $sampleTwo = Sample::getInst(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 128), 'Sample');
    $this->assertSame(128, $sampleTwo->get("bitmask"));

    $sampleThree = new Sample();
    $sampleThree->fromArray(array("id" => 1, "someName" => "/Dee/Eee/Eff", "bitmask" => 256), 'Sample');
    $this->assertSame(256, $sampleThree->get("bitmask"));
  }

  /**
   * Test findOrCreate(). Test user-implemented method.
   *
   * @return void
   */
  public function testFindOrCreate()
  {
    $sampleOne = Sample::getInst()->findOneBy("someName", "/Dee/Eee/Eff");
    $this->assertNull($sampleOne);

    $sampleTwo = Sample::getInst()->findOrCreate("/Dee/Eee/Eff");
    $this->assertInstanceOf("Sample", $sampleTwo);
    $this->assertSame("/Dee/Eee/Eff", $sampleTwo->get("someName"));
  }

  /**
   * Test toArray()
   *
   * @return void
   */
  public function testToArray()
  {
    $sampleOne = Sample::getInst(array("someName" => "/Dee/Eee/Eff"));

    $this->assertSame(array("id" => null, "someName" => "/Dee/Eee/Eff", "bitmask" => null), $sampleOne->toArray());
  }
}