<?php
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
   * Set setup
   *
   * @return void
   */
  public function setUp()
  {
    $simpleDb = SimpleDb::getInst();
    $simpleDb->pdo->exec("DROP TABLE IF EXISTS sample");

    $simpleDb->pdo->beginTransaction();
    $simpleDb->pdo->exec('CREATE TABLE sample ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT null,"someName" TEXT NOT null,"bitmask" INTEGER NOT null DEFAULT (0));');
    $simpleDb->pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uniqueSomeNameIdx ON sample(someName)");
    $simpleDb->pdo->commit();
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

    $samples = Sample::findBy("someName", "/Sample/Two");

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

    $sample = Sample::findOneBy("someName", "/Sample/One");

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

    $sample = Sample::findOneBy("someName", "/Aye/Bee/Cee");

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

    $sample = Sample::findOneBy("someName", "/Sample/One");

    $this->assertInstanceOf("Sample", $sample);
    $this->assertSame("/Sample/One", $sample->get("someName"));
    $this->assertTrue($sample->get("id") > 0);

    $sample->set("someName", "/Sample/Six");
    $id = $sample->save();

    $this->assertSame($sample->get("id"), $id);

    $newSample = Sample::findOneBy("someName", "/Sample/Six");
    $this->assertSame($id, $newSample->get("id"));
  }
}