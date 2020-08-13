<?php

namespace core\components\db;

use Core;
use core\base\Model;

class ActiveRecord extends Model
{
    /**
     * @var string
     */
    public static $table;
    /**
     * @var string
     */
    public $schemaPath;
    /**
     * @var array
     */
    public $schema;

    /**
     * @var array
     */
    protected $_attributes = [];
    /**
     * @var array
     */
    protected $_attributesOld = [];


    /**
     * ActiveRecord constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->schemaPath = Core::$app->getBasePath() . DIRECTORY_SEPARATOR .
            'cache' . DIRECTORY_SEPARATOR . 'schemas';
        parent::__construct($config);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_attributes)) {
            $this->_attributes[$name] = $value;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->_attributes);
    }

    /**
     * Initializes the object.
     */
    public function init()
    {
        parent::init();
        $this->schema = $this->getSchema();

        foreach ($this->schema as $column) {
            $columnName = $column['COLUMN_NAME'];
            $columnDefault = $column['COLUMN_DEFAULT'];
            $this->_attributes[$columnName] = $columnDefault;
            $this->_attributesOld[$columnName] = $columnDefault;
        }
    }

    /**
     * Return a schema of table.
     *
     * @return array
     */
    private function getSchema()
    {
        $table = $this->getTableName();
        $file = $this->schemaPath . DIRECTORY_SEPARATOR . $table;
        if (!is_file($file)) {
            $data = Core::$app->db->getTableInfo($table);
            file_put_contents($file, json_encode($data));
        }
        $schema = file_get_contents($file);

        return json_decode($schema, true);
    }

    /**
     * Finds and creates a new AR-object.
     *
     * @param array|string $idOrConditions
     *
     * @return static|null
     */
    public static function find($idOrConditions)
    {
        if (is_array($idOrConditions)) {
            $conditions = implode(' AND ', $idOrConditions);
        } else {
            $conditions = '`id` = ' . (int)$idOrConditions;
        }
        $row = Core::$app->db->getRow(static::$table, $conditions);
        if (empty($row)) {
            return null;
        }

        $object = new static();
        foreach ($row as $name => $value) {
            $object->_attributes[$name] = $value;
            $object->_attributesOld[$name] = $value;
        }

        return $object;
    }

    /**
     * Save changes into table.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->isNewRecord()) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    /**
     * @return bool
     */
    public function isNewRecord()
    {
        return $this->{'id'} === null;
    }

    /**
     * Inserts a new record.
     *
     * @return bool
     */
    public function insert()
    {
        if ($this->beforeInsert()) {
            $table = $this->getTableName();
            unset($this->_attributes['id']);
            $id = Core::$app->db->insert($table, $this->_attributes);
            $this->_attributes['id'] = $id;
            $this->afterInsert();

            return true;
        }

        return false;
    }

    /**
     * This method is called before insert.
     *
     * @return bool
     */
    public function beforeInsert()
    {
        return true;
    }

    /**
     * This method is called after insert.
     */
    public function afterInsert()
    {
    }

    /**
     * Updates a current record.
     *
     * @return bool
     */
    public function update()
    {
        if ($this->beforeUpdate()) {
            $changeAttributes = $this->getChangeAttributes();
            if (count($changeAttributes) > 0) {
                $table = $this->getTableName();
                $conditions = ['id' => $this->{'id'}];
                Core::$app->db->update(
                    $table,
                    $conditions,
                    $changeAttributes
                );
                $this->afterUpdate();

                return true;
            }
        }

        return false;
    }

    /**
     * This method is called before update.
     *
     * @return bool
     */
    public function beforeUpdate()
    {
        return true;
    }

    /**
     * This method is called after update.
     */
    public function afterUpdate()
    {
    }

    /**
     * Deletes a current record.
     *
     * @return bool
     */
    public function delete()
    {
        if ($this->beforeDelete()) {
            $table = $this->getTableName();
            Core::$app->db->delete($table, ['id' => $this->{'id'}]);
            $this->afterDelete();

            return true;
        }

        return false;
    }

    /**
     * This method is called before delete.
     *
     * @return bool
     */
    public function beforeDelete()
    {
        return true;
    }

    /**
     * This method is called after delete.
     */
    public function afterDelete()
    {
    }

    /**
     * Returns change attributes.
     *
     * @return array
     */
    public function getChangeAttributes()
    {
        $changeAttributes = [];
        foreach ($this->_attributes as $key => $value) {
            if ($this->_attributes[$key] !== $this->_attributesOld[$key]) {
                $changeAttributes[$key] = $value;
            }
        }

        return $changeAttributes;
    }

    /**
     * Returns a table name from this object.
     *
     * @return mixed
     */
    public function getTableName()
    {
        $vars = get_class_vars(get_class($this));

        return $vars['table'];
    }
}