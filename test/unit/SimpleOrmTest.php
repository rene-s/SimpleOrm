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
    $simpleDb = SimpleDb::getInst();

    $sampleDbConfig = SampleDbConfig::getInst($simpleDb);
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

  /**
   * Add test case:
   *
   * 1. Have a table and model with columns a,b,c,d
   * 2. Do a findByQuery() with fields (a,b,c and NO d)
   * 3. Set new value for field b. save.
   * 4. Look into database and see that field d of the record had been wiped (is NULL) because it has not been SELECTed in the query.
   *
   * @return void
   */
  public function testRecordHasNoNullValue()
  {
    $randomInt = mt_rand(4322323, 42234233344);
    $newSomeName = "/dont/have/a/cow/man";

    // create new record, set all fields
    $sampleOne = new Sample();
    $sampleOne->set("someName", "/Aye/Bee/Cee");
    $sampleOne->set("bitmask", $randomInt);
    $sampleOne->save();

    // retrieve all fields of that record again
    $records = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE bitmask = ?", array($randomInt));
    $record = $records[0];

    $id = $record->get("id");

    // change someName, save.
    $record->set("someName", $newSomeName);
    $record->save();

    // get again, verify field values
    $records = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE bitmask = ?", array($randomInt));
    $record = $records[0];

    $this->assertSame($id, $record->get("id"));
    $this->assertSame($newSomeName, $record->get("someName"));
    $this->assertEquals($randomInt, $record->get("bitmask"));

    // now: get the same record, but select id,bitmask only.
    $records = Sample::getInst()->findByQuery("SELECT id,bitmask FROM sample WHERE bitmask = ?", array($randomInt));
    $record = $records[0];

    // field someName has value NULL now. that's expected, since the model has that field and it has not been selected by the query.
    $this->assertNull($record->get("someName"));

    // The issue is, that in the current version (2013-11) of SimpleOrm when I save the record now, field "someName"
    // would get a NULL value *in the database* even though I have not set it to NULL myself. That's not what we want
    // and thus must be intercepted.

    // save record
    try {
      $record->set("bitmask", $randomInt + 1);
      $record->save();

      // get record *again*. The correct result is that "someName" is not NULL but rather has remained the same.
      $records = Sample::getInst()->findByQuery("SELECT * FROM sample WHERE bitmask = ?", array($randomInt + 1));
      $record = $records[0];

      $this->assertEquals($id, $record->get("id"));
      $this->assertEquals($newSomeName, $record->get("someName"));
      $this->assertEquals($randomInt + 1, $record->get("bitmask"));
    } catch (\Exception $e) {
      $this->fail("No exception expected: ", $e->getMessage());
    }
  }

  /**
   * Do not process model instance fields that still are NULL. getPayload() must filter them by default,
   * except the PK field which may be null when inserting.
   */
  public function testGetPayloadRemoveNullRetainPk()
  {
    $sampleOne = new Sample();
    $sampleOne->set("bitmask", 123);

    // remove NULL but retain PK
    $payload = $sampleOne->getPayload();

    $this->assertNull($payload['id']);
    // because the field is NULL, it has been removed because when updating, it would cause problems:
    $this->assertArrayNotHasKey('someName', $payload);
    $this->assertArrayHasKey('bitmask', $payload);
    $this->assertSame(123, $payload['bitmask']);
  }

  /**
   * Instruct getPayload() to remove nothing.
   */
  public function testGetPayloadRemoveNothing()
  {
    $sampleOne = new Sample();
    $sampleOne->set("bitmask", 123);

    $payload = $sampleOne->getPayload(Sample::PAYLOAD_ORIGINAL);
    $this->assertArrayHasKey('id', $payload);
    $this->assertNull($payload['id']);
    $this->assertArrayHasKey('someName', $payload);
    $this->assertNull($payload['someName']);

    // also remove ID field
    $payload = $sampleOne->getPayload(Sample::PAYLOAD_CLEAN);

    $this->assertArrayHasKey('bitmask', $payload);
    $this->assertArrayNotHasKey('someName', $payload);
    $this->assertArrayNotHasKey('id', $payload);
  }

  /**
   * Instruct getPayload() to even remove the PK field when it is NULL
   */
  public function testGetPayloadRemoveNull()
  {
    $sampleOne = new Sample();
    $sampleOne->set("bitmask", 123);

    // also remove ID field
    $payload = $sampleOne->getPayload(Sample::PAYLOAD_CLEAN);

    $this->assertArrayHasKey('bitmask', $payload);
    $this->assertArrayNotHasKey('someName', $payload);
    $this->assertArrayNotHasKey('id', $payload);
  }
}