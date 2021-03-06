<?php


namespace Fredy\Model\Entity;


abstract class Entity implements \ArrayAccess, \Iterator
{
    /**
     * @var $fields Field[]
     */
    protected $fields;

    public $tableName;

    public function addField($field)
    {
        $this->fields[$field->name] = $field;
    }

    public function isValid()
    {
        $valid = true;
        foreach ($this->fields as $name => $field) {
            if (!$field->isValid()) {
                $valid = false;
            }
        }
        return $valid;
    }

    public function __get($property)
    {
        return $this->fields[$property];
    }

    public function __isset($property)
    {
        if (property_exists($this, $property)
            || array_key_exists($property, $this->fields)
        ) {
            return true;
        }
        return false;
    }

    public function fill($data) {
        foreach ($data as $attribute => $value) {
            if (array_key_exists($attribute, $this->fields)) {
                $this->fields[$attribute]->value = $value;
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return Field
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Field
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->fields[key($this->fields)]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->fields);
    }

    /**
     * Gets a array with the database name for all the fields of the entity for the repository.
     * @return array
     */
    public function getFieldDatabaseNameArray()
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $fields[] = $field->toSelectString();
        }
        return $fields;
    }

    public function getValueArray()
    {
        $fields = [];
        foreach ($this->fields as $key => $field) {
            $fields[] = $field->toInsertString();
        }
        return $fields;
    }

    function sortByFieldsIndex($a, $b)
    {
        return $this->fields[$a]->index >= $this->fields[$b]->index ? 1 : -1;
    }

}