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

use Closure;

/**
 * Simple ORM.
 *
 * Be aware that this ORM class expects every entity to have a numeric PK field with name "id".
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
abstract class SimpleOrm
{
    const PAYLOAD_ORIGINAL = 0; // do not modify payload
    const PAYLOAD_CLEAN = 1; // removes all NULL values
    const PAYLOAD_CLEAN_SMART = 2; // removes all NULL values but not from PK field

    /**
     * Array with table fields
     *
     * @var array
     */
    protected $payload = array();

    /**
     * @var string
     */
    protected static $table = "set_table_in_model_derived_from_this_class";

    /**
     * @var string
     */
    protected $pkFieldName = "";

    /**
     * @var Closure|null
     */
    protected $findFilter = null;

    /**
     * Constructor
     *
     * @param array $data Array with data to be set
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }

        $this->setPkFieldName();
    }

    /**
     * Set PK field name. Is usually the first field given.
     *
     * @return void
     */
    public function setPkFieldName()
    {
        $keys = array_keys($this->payload);
        $this->pkFieldName = $keys[0];
    }

    /**
     * Create instance
     *
     * @param array $data Data array
     *
     * @return static
     * @static
     * @throws \Exception
     */
    public static function getInst(array $data = array())
    {
        return new static($data);
    }

    /**
     * Getter
     *
     * @param string     $what    Name of property to get
     * @param mixed|null $default Default value in case property does not exist
     *
     * @return mixed|null
     */
    public function get($what, $default = null)
    {
        if (array_key_exists($what, $this->payload)) {
            return $this->payload[$what];
        }

        return $default;
    }

    /**
     * Setter
     *
     * @param string $what  Name of property to get
     * @param mixed  $value Value to be set
     *
     * @return void
     */
    public function set($what, $value)
    {
        if (array_key_exists($what, $this->payload)) {
            $this->payload[$what] = $value;
        }
    }

    /**
     * Populate properties from array
     *
     * @param array $data Array with data to be set
     *
     * @return void
     */
    public function fromArray(array $data)
    {
        foreach ($data as $field => $value) {
            $this->set($field, $value);
        }
    }

    /**
     * Find one record
     *
     * @param string $field     Field name
     * @param mixed  $value     Value
     * @param int    $fetchMode Fetch mode
     *
     * @return null|SimpleOrm
     */
    public function findOneBy($field, $value, $fetchMode = \PDO::FETCH_OBJ)
    {
        $result = $this->findBy($field, $value, $fetchMode);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    /**
     * Find records
     *
     * @param string $field     Field name
     * @param mixed  $value     Value
     * @param int    $fetchMode Fetch mode
     *
     * @return SimpleOrm[]
     */
    public function findBy($field, $value, $fetchMode = \PDO::FETCH_OBJ)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE " . $field . " = ?";

        return $this->findByQuery($query, array($value), $fetchMode);
    }

    /**
     * Find records by query
     *
     * @param string $query     SQL Query
     * @param array  $values    Values
     * @param int    $fetchMode Fetch mode
     *
     * @return SimpleOrm[]
     */
    public function findByQuery($query, array $values, $fetchMode = \PDO::FETCH_OBJ)
    {
        $sth = SimpleDb::getInst()->pdo->prepare($query);
        $sth->execute($values);

        return $this->collectRecords($sth, $fetchMode);
    }

    /**
     * Set find filter
     * @param Closure $filter Filter closure
     * @return self
     */
    public function setFilter(Closure $filter = null)
    {
        $this->findFilter = $filter;
        return $this;
    }

    /**
     * Collect records
     *
     * @param \PDOStatement $sth       PDOStatement instance
     * @param int           $fetchMode Fetch mode
     *
     * @return SimpleOrm[]
     */
    protected function collectRecords(
        \PDOStatement $sth,
        $fetchMode = \PDO::FETCH_OBJ
    ) {
        $returnResults = array();
        $rawResults = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_null($this->findFilter)) {
            $amount = count($rawResults);
            for ($i = 0; $i < $amount; $i++) {
                $rawResults[$i] = call_user_func($this->findFilter, $rawResults[$i]);
            }
        }

        if ($fetchMode === \PDO::FETCH_ASSOC) {
            return $rawResults;
        }

        foreach ($rawResults as $rawResult) {
            $returnResults[] = self::getInst($rawResult);
        }

        return $returnResults;
    }

    /**
     * Insert record
     *
     * @return int Last record ID
     */
    public function insert()
    {
        $sql = "INSERT INTO " . "%s" . " (%s) VALUES (%s)";

        $placeholders = array_fill(0, count($this->payload), '?');

        $id = $this->execute(
            sprintf($sql, static::$table, implode(",", array_keys($this->payload)), implode(",", $placeholders))
        );

        $this->set($this->pkFieldName, $id);

        return $id;
    }

    /**
     * Update record
     *
     * @return int Last record ID
     */
    public function update()
    {
        // only numeric PKs are supported and their name must be "id".
        $sql = "UPDATE " . "%s" . " SET " . "%s" . " WHERE %s = " . "%d";

        // remove NULL value fields from model instance so we do not set them NULL in the DB.
        $placeholders = array_keys($this->getPayload());

        foreach ($placeholders as $k => $val) {
            $placeholders[$k] = sprintf("%s = ?", $val);
        }

        return $this->execute(
            sprintf(
                $sql,
                static::$table,
                implode(",", $placeholders),
                $this->pkFieldName,
                $this->get($this->pkFieldName)
            )
        );
    }

    /**
     * Get model instance payload
     *
     * @param int $cleanMode Clean nothing from payload or NULL values or NULL values except PK field.
     *
     * @return array
     */
    public function getPayload($cleanMode = self::PAYLOAD_CLEAN_SMART)
    {
        $result = array();

        foreach ($this->payload as $key => $value) {
            if (!is_null($value)
                || ($cleanMode === self::PAYLOAD_CLEAN_SMART && $key === $this->pkFieldName)
                || $cleanMode === self::PAYLOAD_ORIGINAL
            ) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Delete record
     *
     * @return bool
     */
    public function del()
    {
        if (!$this->get($this->pkFieldName)) {
            return false;
        }

        $sql = sprintf("DELETE FROM " . "%s" . " WHERE " . $this->pkFieldName . " = ?", static::$table);

        SimpleDb::getInst()->pdo->prepare($sql)->execute(array($this->get($this->pkFieldName)));

        $this->set($this->pkFieldName, null);

        return true;
    }

    /**
     * Execute SQL query
     *
     * @param string $sql SQL query
     *
     * @return int Last record ID
     */
    public function execute($sql)
    {
        $pdo = SimpleDb::getInst()->pdo;
        $sth = $pdo->prepare($sql);

        // save PK field value. Then remove NULL value fields from model instance so we do not set them NULL in the DB.
        $sth->execute(array_values($this->getPayload()));

        return $pdo->lastInsertId();
    }

    /**
     * Save record
     *
     * @return int Last record ID
     */
    public function save()
    {
        if ($this->get($this->pkFieldName)) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Return instance as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->payload;
    }
}
