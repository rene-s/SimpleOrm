<?php
/**
 * SimpleOrm
 *
 * PHP Version 5.5
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
namespace SimpleOrm\Tests\SimpleOrm;

use SimpleOrm\SimpleDb;
use SimpleOrm\Tests\Sample;
use SimpleOrm\Tests\SampleDbConfig;

/**
 * SimpleOrm test with actual queries
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
class SimpleOrmQueriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var bool
     */
    protected $isMySql = false;

    /**
     * Test DSN for DB
     * @var string
     */
    protected $dsn = 'sqlite::memory:';

    /**
     * Set setup
     *
     * @return void
     */
    public function setUp()
    {
        $simpleDb = SimpleDb::getInst($this->dsn);

        $sampleDbConfig = SampleDbConfig::getInst($simpleDb);
        $sampleDbConfig->setUp();

        $this->isMySql = preg_match("/^mysql:/", $this->dsn) > 0;
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
            "INSERT " . "INTO sample ("
            . implode(",", array_keys($data)) . ") VALUES ('"
            . implode("','", array_values($data)) . "')"
        );
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
        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $samples[0]);
        $this->assertSame("/Sample/Two", $samples[0]->get("someName"));
    }

    /**
     * Test findBy, return results as array
     *
     * @return void
     */
    public function testFindByReturnAsArray()
    {
        $this->createRecord(array("id" => 1, "someName" => "/Sample/Two"));

        $samples = Sample::getInst()->findBy("someName", "/Sample/Two", \PDO::FETCH_ASSOC);

        $this->assertInternalType("array", $samples);
        $this->assertSame(1, count($samples));
        $this->assertInternalType("array", $samples[0]);
        $this->assertSame("/Sample/Two", $samples[0]["someName"]);
    }

    /**
     * Test findBy with filter, with array
     *
     * @return void
     */
    public function testFindByWithFilterAsArray()
    {
        $this->createRecord(array("id" => 1, "someName" => "/Sample/Two"));

        $samples = Sample::getInst()
            ->setFilter(function ($inst) {
                $inst['someName'] .= 'x';
                return $inst;
            })
            ->findBy("someName", "/Sample/Two", \PDO::FETCH_ASSOC);

        $this->assertInternalType("array", $samples);
        $this->assertSame(1, count($samples));
        $this->assertInternalType("array", $samples[0]);
        $this->assertSame("/Sample/Twox", $samples[0]["someName"]);
    }

    /**
     * Test findBy with filter, with objects
     *
     * @return void
     */
    public function testFindByWithFilter()
    {
        $this->createRecord(array("id" => 2, "someName" => "/Sample/Three"));

        $samples = Sample::getInst()->setFilter(function ($inst) {
            $inst['someName'] .= 'x';
            return $inst;
        })->findBy("someName", "/Sample/Three");

        $this->assertInternalType("array", $samples);
        $this->assertSame(1, count($samples));
        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $samples[0]);
        $this->assertSame("/Sample/Threex", $samples[0]->get("someName"));
    }

    /**
     * Test findBy with filter, but without results
     *
     * @return void
     */
    public function testFindByWithFilterWithoutResults()
    {
        $samples = Sample::getInst()->setFilter(function ($inst) {
            $inst['someName'] .= 'x';
            return $inst;
        })->findBy("someName", "doesnotexist");

        $this->assertInternalType("array", $samples);
        $this->assertSame(0, count($samples));
    }

    /**
     * Test default get()
     *
     * @return void
     */
    public function testGet()
    {
        $newSample = new Sample();
        $newSample->set("someName", "/Aye/Bee/Cee");

        $this->assertSame('/Aye/Bee/Cee', $newSample->get('someName'));
        $this->assertSame('default', $newSample->get('doesnotexist', 'default'));
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

        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $sample);
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

        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $sample);
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

        $samples = Sample::getInst()->findByQuery("SELECT * " . "FROM sample WHERE id = ?", array(1));

        $this->assertInternalType("array", $samples);
        $this->assertSame(1, count($samples));
        $this->assertEquals(1, $samples[0]->get("id"));

        $samples = Sample::getInst()->findByQuery("SELECT * " . "FROM sample WHERE id > ?", array(1));

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
     * Test findOrCreate(). Test user-implemented method.
     *
     * @return void
     */
    public function testFindOrCreate()
    {
        $sampleOne = Sample::getInst()->findOneBy("someName", "/Dee/Eee/Eff");
        $this->assertNull($sampleOne);

        $sampleTwo = Sample::getInst()->findOrCreate("/Dee/Eee/Eff");
        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $sampleTwo);
        $this->assertSame("/Dee/Eee/Eff", $sampleTwo->get("someName"));
    }

    /**
     * Add test case:
     *
     * 1. Have a table and model with columns a,b,c,d
     * 2. Do a findByQuery() with fields (a,b,c and NO d)
     * 3. Set new value for field b. save.
     * 4. Look into database and see that field d of the record had been wiped
     *    (is NULL) because it has not been SELECTed in the query.
     *
     * @return void
     */
    public function testRecordHasNoNullValue()
    {
        $randomInt = mt_rand(23456, 567890);
        $newSomeName = "/dont/have/a/cow/man";

        // create new record, set all fields
        $sampleOne = new Sample();
        $sampleOne->set("someName", "/Aye/Bee/Cee");
        $sampleOne->set("bitmask", $randomInt);
        $sampleOne->save();

        // retrieve all fields of that record again
        $records = Sample::getInst()->findByQuery("SELECT * " . "FROM sample WHERE bitmask = ?", array($randomInt));
        $record = $records[0];

        $id = $record->get("id");

        // change someName, save.
        $record->set("someName", $newSomeName);
        $record->save();

        // get again, verify field values
        $records = Sample::getInst()->findByQuery("SELECT * " . "FROM sample WHERE bitmask = ?", array($randomInt));
        $record = $records[0];

        $this->assertSame($id, $record->get("id"));
        $this->assertSame($newSomeName, $record->get("someName"));
        $this->assertEquals($randomInt, $record->get("bitmask"));

        // now: get the same record, but select id,bitmask only.
        $records = Sample::getInst()->findByQuery(
            "SELECT id,bitmask " . "FROM sample WHERE bitmask = ?",
            array($randomInt)
        );

        $record = $records[0];

        // field someName has value NULL now. that's expected, since the
        // model has that field and it has not been selected by the query.
        $this->assertNull($record->get("someName"));

        // The issue is, that in the current version (2013-11) of SimpleOrm when I save the record now, field "someName"
        // would get a NULL value *in the database* even though I have not set it to NULL myself.
        // That's not what we want and thus must be intercepted.

        // save record
        try {
            $record->set("bitmask", $randomInt + 1);
            $record->save();

            // get record *again*. The correct result is that
            // "someName" is not NULL but rather has remained the same.
            $records = Sample::getInst()->findByQuery(
                "SELECT * " . "FROM sample WHERE bitmask = ?",
                array($randomInt + 1)
            );

            $record = $records[0];

            $this->assertEquals($id, $record->get("id"));
            $this->assertEquals($newSomeName, $record->get("someName"));
            $this->assertEquals($randomInt + 1, $record->get("bitmask"));
        } catch (\Exception $e) {
            $this->fail("No exception expected: " . $e->getMessage());
        }
    }
}
