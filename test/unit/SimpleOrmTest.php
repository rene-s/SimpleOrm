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
namespace SimpleOrmTest;

use SimpleOrm\SimpleDb;
use SimpleOrm\SimpleDbConfig;
use SimpleOrm\Tests\Sample;
use SimpleOrm\Tests\SampleDbConfig;

/**
 * SimpleOrm test
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
class SimpleOrmTest extends \PHPUnit_Framework_TestCase
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
            "INSERT INTO sample ("
            . implode(",", array_keys($data)) . ") VALUES ('"
            . implode("','", array_values($data)) . "')"
        );
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

        $this->assertInstanceOf("\\SimpleOrm\\Tests\\Sample", $sample);
        $this->assertSame("/Sample/One", $sample->get("someName"));
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
     * Do not process model instance fields that still are NULL. getPayload() must filter them by default,
     * except the PK field which may be null when inserting.
     * @return void
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
     * @return void
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
     * @return void
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

    /**
     * Verify that cloning of an SimpleDb instance is forbidden
     * @return void
     */
    public function testCloningSimpleDbForbidden()
    {
        $simpleDb = SimpleDb::getInst();

        try {
            $cloned = clone $simpleDb;
            $this->fail("Exception expected, cloning SimpleDb is forbidden");
        } catch (\Exception $e) {
            $this->assertInstanceOf("\Exception", $e);
        }
    }

    /**
     * Verify that cloning of an SimpleDbConfig instance is forbidden
     * @return void
     */
    public function testCloningSimpleDbConfigbForbidden()
    {
        $simpleDb = SimpleDb::getInst();
        $simpleDbConfig = SimpleDbConfig::getInst($simpleDb);

        try {
            $cloned = clone $simpleDbConfig;
            $this->fail("Exception expected, cloning SimpleDbConfig is forbidden");
        } catch (\Exception $e) {
            $this->assertInstanceOf("\Exception", $e);
        }
    }
}
