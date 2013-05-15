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
    if (SampleDb::$dbPath === ":memory:") {
      SampleDb::getInst()->db->exec("DROP TABLE sample");
    } else {
      $dir = dirname(SampleDb::$dbPath);

      if (!file_exists($dir)) {
        mkdir($dir);
      }

      if (file_exists(SampleDb::$dbPath)) {
        unlink(SampleDb::$dbPath);
      }
    }

    SampleDb::getInst()->__destruct();
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
    $pdo = SampleDb::getInst()->db;

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
    $pdo = SampleDb::getInst()->db;

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
}