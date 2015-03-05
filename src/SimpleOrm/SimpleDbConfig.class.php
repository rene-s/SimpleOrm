<?php

namespace SimpleOrm;

/**
 * Config for SimpleDb
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
    protected $_simpleDb = null;

    /**
     * Do not use
     */
    private function __construct(SimpleDb $simpleDb)
    {
        $this->_simpleDb = $simpleDb;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        static::$instance = null;
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