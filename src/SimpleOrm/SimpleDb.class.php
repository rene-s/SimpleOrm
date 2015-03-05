<?php

namespace SimpleOrm;

/**
 * Db class. Geared towards sqlite. This class does nothing special.
 *
 * @package SimpleOrm
 * @author  Rene Schmidt <github@reneschmidt.de>
 */
class SimpleDb
{
    /**
     * Instance container
     *
     * @var SimpleDb
     */
    protected static $instance = null;

    /**
     * DB connection
     *
     * @var \PDO
     */
    public $pdo = null;

    /**
     * Do not use
     */
    private function __construct()
    {
        $this->createDbConn();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        self::$instance = null;
    }

    /**
     * Create SimpleDb instance
     *
     * @static
     * @return SimpleDb
     */
    public static function getInst()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Disable cloning
     *
     * @return void
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception("Cloning is forbidden", 1);
    }

    /**
     * Create DB conn.
     *
     * Creates tables if necessary
     *
     * @return void
     * @throws \Exception
     */
    public function createDbConn()
    {
        if (!defined("DB_DSN")) {
            throw new \Exception("DB_DSN has not been set. Look up docs on how to set up DB connection");
        }

        $requireCredentials = preg_match("/^mysql:/i", DB_DSN) > 0;

        if ($requireCredentials && (!defined('DB_USER') || !defined('DB_PASS'))) {
            throw new \Exception("DB_USER and DB_PASS have not been set. Look up docs on how to set up DB connection");
        }

        if ($requireCredentials) {
            $this->pdo = new \PDO(DB_DSN, DB_USER, DB_PASS);
        } else {
            $this->pdo = new \PDO(DB_DSN);
        }

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}