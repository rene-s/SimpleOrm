<?php
use SimpleOrm\SimpleDb;
use SimpleOrm\SimpleDbConfig;
use SimpleOrm\Tests\Sample;
use SimpleOrm\Tests\SampleDbConfig;

/**
 * SimpleOrm test
 *
 * @package    SimpleOrm
 * @subpackage TestUnit
 * @author     Rene Schmidt <rene@reneschmidt.de>
 */
class SimpleDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tear down stuff after each test.
     */
    public function tearDown()
    {
        $this->tearDownSimpleDb();
        parent::tearDown();
    }

    /**
     * Tear down SimpleDb instance after each test.
     */
    protected function tearDownSimpleDb()
    {
        try {
            $simpleDb = SimpleDb::getInst();
            $simpleDb->destroy();
        } catch (\Exception $e) {
            // if there is no instance, everything is OK
        }
    }

    /**
     * CreateConn
     */
    public function testCreateDbConnOk()
    {
        $dsn = 'sqlite::memory:';
        $simpleDb = SimpleDb::getInst($dsn);

        // everything's OK
        $this->assertInstanceOf("SimpleOrm\\SimpleDb", $simpleDb);
    }

    /**
     * No DSN given
     */
    public function testCreateDbConnNoDsnGiven()
    {
        try {
            $simpleDb = SimpleDb::getInst();
        } catch (\Exception $e) {
            $this->assertSame("No DSN given", $e->getMessage());
        }
    }

    /**
     * No DSN given
     */
    public function testCreateDbConnNoDsn()
    {
        $dsn = 'sqlite::memory:';
        $simpleDb = SimpleDb::getInst($dsn);

        // everything's OK, at first...
        $this->assertInstanceOf("SimpleOrm\\SimpleDb", $simpleDb);

        try {
            $simpleDb->createDbConn(''); // empty DSN is invalid
        } catch (\Exception $e) {
            $this->assertSame(
                'DSN has not been set. Look up docs on how to set up DB connection',
                $e->getMessage()
            );
        }
    }

    /**
     * login credentials are required but not given
     */
    public function testCreateDbConnNoLoginCredentials()
    {
        $dsn = 'sqlite::memory:';
        $simpleDb = SimpleDb::getInst($dsn);

        // everything's OK, at first...
        $this->assertInstanceOf("SimpleOrm\\SimpleDb", $simpleDb);

        try {
            // sneak in new DSN
            $dsn = 'mysql:host=localhost;port=3306;dbname=testdb';
            $simpleDb->createDbConn($dsn);
        } catch (\Exception $e) {
            $this->assertSame(
                "User or pass have not been set. Look up docs on how to set up DB connection",
                $e->getMessage()
            );
        }
    }
} 