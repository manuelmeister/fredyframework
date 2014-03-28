<?php


namespace Model\Repository;

// #@todo: createAll, updateAll, removeAll


abstract class Repository
{

    public $lastInsertId;

    /**
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * @var \Model\Entity\Entity
     */
    protected $entity;

    protected $entityFields;

    /**
     * @var $database \PDO
     */
    protected $database;

    /**
     * @var $factory \Model\Factory\Factory
     */
    protected $factory;

    /**
     * @internal param \PDO $database
     */
    public function __construct($database)
    {
        if (empty($this->tableName)) {
            throw new NoTableNameDefinedException();
        }
        $this->database = $database;
        $this->entityFields = $this->entity->getFieldDatabaseNameArray();
    }

    /**
     * @param $limit
     * @param $offset
     *
     * @return \Model\Entity\Entity[]
     */
    public function findAll($limit = 0, $offset = 0)
    {
        $limit = $this->getLimit($limit, $offset);
        $query = 'SELECT ' . $this->getFields(", ") . '
                  FROM ' . $this->tableName . '
                  ' . $limit . ' ;';
        $statement = $this->database->prepare($query);
        $statement->execute();
        return $this->factory->buildAll($statement->fetchAll());
    }

    /**
     * @param $id
     *
     * @return \Model\Entity\Entity
     */
    public function findById($id)
    {
        $query =
            'SELECT `' .
            $this->getFields("`, `") . '`
            FROM ' . $this->tableName .
            ' WHERE `id`=:id LIMIT 1';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $this->factory->build($statement->fetch());
    }

    /**
     * @param $filter \Model\Repository\Filter
     *
     * @param int $limit
     * @param int $offset
     * @return \Model\Entity\Entity
     */
    public function findByFilter($filter, $limit = 0, $offset = 0)
    {
        $limit = $this->getLimit($limit, $offset);
        $query =
            'SELECT ' .
            $this->getFields(", ") .
            ' FROM ' .
            $this->tableName .
            ' WHERE ' .
            join(" AND ", $filter->getConditionArray()) .
            ' ' . $limit . ';';
        $statement = $this->database->prepare($query);
        $statement->execute();
        return $this->factory->buildAll($statement->fetchAll());
    }

    /**
     * @param $entity \Model\Entity\Entity
     * @return bool
     */
    public function create($entity)
    {
        return $this->applyEntityToDatabase($entity, false);
    }

    /**
     * @param $entity \Model\Entity\Entity
     * @return bool
     */
    public function update($entity)
    {
        return $this->applyEntityToDatabase($entity, true);
    }

    /**
     * @param $entity \Model\Entity\Entity
     *
     * @param $update
     * @return boolean
     */
    private function applyEntityToDatabase($entity, $update)
    {
        if (!$entity->isValid()) {
            // #@todo handle somehow, maybe throw exception?
            return false;
        }
        if ($update) {
            $command = 'REPLACE INTO';
        } else {
            $command = 'INSERT INTO';
        }
        $query =
            $command . ' `' . $this->tableName . '` (`' .
            $this->getFields("`, `") .
            '`) VALUES (:' .
            $this->getFields(", :") .
            ');';
        $statement = $this->database->prepare($query);
        $valueArray = $entity->getValueArray();
        foreach ($this->entityFields as $index => $paramName) {
            $statement->bindParam(':' . $paramName, $valueArray[$index]);
        }
        $isSuccessful = $statement->execute();
        // #@todo throw and exception if $stmt->errorInfo() has an error?
        // var_dump($statement->errorInfo());

        $this->lastInsertId = $this->database->lastInsertId();

        return $isSuccessful;
    }


    /**
     * Remove an entity from the database using the id of the entity.
     * @param $entity \Model\Entity\Entity
     *
     * @return void
     */
    public function remove($entity)
    {
        if ($entity->isValid() && is_numeric($entity['id']->value)) {
            $query = 'DELETE FROM `' . $this->tableName . '` WHERE `id`=:id';
            $statement = $this->database->prepare($query);
            $statement->bindParam(':id', $entity['id']->value);
            return $statement->execute();
            // #@todo throw and exception if $stmt->errorInfo() has an error?
            // var_dump($stmt->errorInfo());
        } else {
            // #@todo: handle invalid entity
            // #@todo: handle invalid id
        }
    }

    /**
     * @param $limit integer
     * @param $offset integer
     * @return string
     */
    private function getLimit($limit, $offset)
    {
        if (!($limit == 0 && $offset == 0)) {
            return 'LIMIT ' . intval($offset) . ', ' . intval($limit);
        } else {
            return '';
        }
    }

    private function getFields($glue)
    {
        return join($glue, $this->entityFields);
    }
}

Class NoTableNameDefinedException extends \Exception
{

}