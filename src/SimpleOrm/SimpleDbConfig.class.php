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
namespace SimpleOrm;

/**
 * Config for SimpleDb
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
abstract class SimpleDbConfig
{
    /**
     * Instance container
     *
     * @var SimpleDbConfig
     */
    protected static $instance = null;

    /**
     * @var SimpleDb
     */
    protected $simpleDb = null;

    /**
     * Do not use
     * @param SimpleDb $simpleDb SimpleDB instance
     */
    private function __construct(SimpleDb $simpleDb)
    {
        $this->simpleDb = $simpleDb;
    }

    /**
     * Create RuntimeData instance
     *
     * @param SimpleDb $simpleDb instance
     *
     * @return SimpleDbConfig
     * @static
     * @throws \Exception
     */
    public static function getInst(SimpleDb $simpleDb)
    {
        if (null === static::$instance) {
            static::$instance = new static($simpleDb);
        }

        return static::$instance;
    }

    /**
     * Disable cloning
     *
     * @return void
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception("Cloning is forbidden", 10010);
    }

    /**
     * Sets up all the tables
     *
     * @return void
     * @abstract
     */
    abstract public function setUp();
}
