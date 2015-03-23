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
    private function __construct($dsn, $user = '', $pass = '')
    {
        $this->createDbConn($dsn, $user, $pass);
    }

    /**
     * Create SimpleDb instance
     *
     * @param string $dsn  DSN string
     * @param string $user User name
     * @param string $pass Password
     * @static
     * @return SimpleDb
     * @throws \Exception
     */
    public static function getInst($dsn = '', $user = '', $pass = '')
    {
        if (null === self::$instance) {
            if (empty($dsn)) {
                throw new \Exception('No DSN given');
            }
            self::$instance = new self($dsn, $user, $pass);
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
     * Destroy
     */
    public function destroy()
    {
        self::$instance = null;
    }

    /**
     * Create DB conn.
     *
     * Creates tables if necessary
     *
     * @return void
     * @throws \Exception
     */

    /**
     * Create DB conection
     * @param string $dsn  DSN string
     * @param string $user User name
     * @param string $pass Password
     * @throws \Exception
     */
    public function createDbConn($dsn, $user = '', $pass = '')
    {
        if (empty($dsn)) {
            throw new \Exception("DSN has not been set. Look up docs on how to set up DB connection");
        }

        $requireCredentials = preg_match("/^mysql:/i", $dsn) > 0;

        if ($requireCredentials && (!empty($user) || !empty($pass))) {
            throw new \Exception("User or pass have not been set. Look up docs on how to set up DB connection");
        }

        if ($requireCredentials) {
            $this->pdo = new \PDO($dsn, $user, $pass);
        } else {
            $this->pdo = new \PDO($dsn);
        }

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
