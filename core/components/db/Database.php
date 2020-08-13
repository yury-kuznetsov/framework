<?php

namespace core\components\db;

use core\base\Component;
use PDO;

class Database extends Component
{
    /**
     * @var string
     */
    public $dsn;
    /**
     * @var string
     */
    public $user;
    /**
     * @var string
     */
    public $password;

    /**
     * @var PDO
     */
    protected $pdo;


    /**
     * Database constructor.
     *
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param array  $config
     */
    public function __construct($dsn, $user, $password, $config = [])
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        parent::__construct($config);
    }

    /**
     * Initializes the PDO.
     */
    public function open()
    {
        if ($this->pdo === null) {
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $this->pdo = new PDO(
                $this->dsn, $this->user, $this->password, $options
            );
        }
    }

    /**
     * Runs a SQL script with parameters.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return array
     */
    public function query($sql, $params)
    {
        $this->open();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Returns a row.
     *
     * @param string $table
     * @param string $conditions
     * @param string $select
     *
     * @return array
     */
    public function getRow($table, $conditions = '', $select = '')
    {
        $this->open();
        $sql = $this->buildSelectSql($table, $conditions, $select);

        return $this->pdo->query($sql)->fetch();
    }

    /**
     * Returns rows.
     *
     * @param string $table
     * @param string $conditions
     * @param string $select
     *
     * @return array
     */
    public function getRows($table, $conditions = '', $select = '')
    {
        $this->open();
        $sql = $this->buildSelectSql($table, $conditions, $select);

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Inserts a new record.
     *
     * @param string $table
     * @param array  $data
     *
     * @return string
     */
    public function insert($table, $data)
    {
        $this->open();
        $sql = $this->buildInsertSql($table, $data);
        $this->pdo->exec($sql);

        return $this->pdo->lastInsertId();
    }

    /**
     * Updates a current record.
     *
     * @param string $table
     * @param array  $conditions
     * @param array  $data
     *
     * @return bool
     */
    public function update($table, $conditions, $data)
    {
        $this->open();
        $sql = $this->buildUpdateSql($table, $conditions, $data);
        $this->pdo->query($sql);

        return true;
    }

    /**
     * Deletes a current record.
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return bool
     */
    public function delete($table, $conditions)
    {
        $this->open();
        $sql = $this->buildDeleteSql($table, $conditions);
        $this->pdo->query($sql);

        return true;
    }

    /**
     * Returns a information of table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getTableInfo($table)
    {
        $this->open();
        $dbName = $this->getDsnAttribute('dbname');
        $sql = '
            SELECT * FROM `information_schema`.`columns` 
            WHERE 
                `TABLE_NAME` = "' . htmlspecialchars($table) . '" AND 
                `TABLE_SCHEMA` = "' . htmlspecialchars($dbName) . '"
        ';
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Return a value of DSN-attribute.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getDsnAttribute($name)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $this->dsn, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Builds a new insert query.
     *
     * @param string $table
     * @param string $conditions
     * @param string $select
     *
     * @return string
     */
    private function buildSelectSql($table, $conditions = '', $select = '')
    {
        $dbName = $this->getDsnAttribute('dbname');
        $table = htmlspecialchars($table);
        $fromStr = '`' . $dbName . '`.`' . $table . '`';

        $selectStr = $select !== '' ? $select : '*';
        $whereStr = $conditions !== '' ? 'WHERE ' . $conditions : '';

        return 'SELECT ' . $selectStr . ' FROM ' . $fromStr . ' ' . $whereStr;
    }

    /**
     * Builds a new insert query.
     *
     * @param string $table
     * @param array  $data
     *
     * @return string
     */
    private function buildInsertSql($table, $data)
    {
        $table = htmlspecialchars($table);
        $params = $this->dataToString($data);

        return 'INSERT INTO `' . $table . '` SET ' . $params;
    }

    /**
     * Builds a new update query.
     *
     * @param string $table
     * @param array  $conditions
     * @param array  $data
     *
     * @return string
     */
    private function buildUpdateSql($table, $conditions, $data)
    {
        $table = htmlspecialchars($table);
        $params = $this->dataToString($data);
        $where = $this->conditionsToString($conditions);

        return 'UPDATE `' . $table . '` SET ' . $params . ' WHERE ' . $where;
    }

    /**
     * Builds a new delete query.
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return string
     */
    private function buildDeleteSql($table, $conditions)
    {
        $table = htmlspecialchars($table);
        $where = $this->conditionsToString($conditions);

        return 'DELETE FROM `' . $table . '` WHERE ' . $where;
    }

    /**
     * Converts an associative array to a string.
     *
     * @param array $data
     *
     * @return string
     */
    private function dataToString($data)
    {
        $strings = [];
        foreach ($data as $name => $value) {
            $name = $this->parseParamName($name);
            $value = $this->parseParamValue($value);
            $strings[] = $name . ' = ' . $value;
        }

        return implode(', ', $strings);
    }

    /**
     * Converts an associative array to a string.
     *
     * @param array $conditions
     *
     * @return string
     */
    private function conditionsToString($conditions)
    {
        $strings = [];
        foreach ($conditions as $name => $value) {
            $name = $this->parseParamName($name);
            $value = $this->parseParamValue($value);
            $strings[] = $name . ' = ' . $value;
        }

        return implode(' AND ', $strings);
    }

    /**
     * Parses a name of parameter.
     *
     * @param string $name
     *
     * @return string
     */
    private function parseParamName($name)
    {
        return '`' . htmlspecialchars($name) . '`';
    }

    /**
     * Parses a value of parameter.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function parseParamValue($value)
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        } elseif (is_int($value)) {
            return $value;
        } else {
            return '"' . htmlspecialchars($value) . '"';
        }
    }
}