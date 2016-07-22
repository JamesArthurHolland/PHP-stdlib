<?php

namespace AE\Stdlib;

abstract class MysqlDbAbstract {
    private $new_link = true;
    private $client_flags = 0;
    public $last_sql;
    public $encryption = "md5";
    protected $host = "localhost";
    protected $port = 80;
    protected $user = "root";
    protected $pass = "password";
    protected $dbName  = "alfalfaExample";
    public $link;


    public function __construct() {
    }

    public function __destruct() {
        $this->close();
    }

    public function connect($dbName) {
        $this->link = new \PDO('mysql:host=' . $this->host . ';dbname=' . $dbName . ';charset=utf8', $this->user, $this->pass);
        $this->link->setAttribute(\PDO::ATTR_EMULATE_PREPARES,TRUE);
        $this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
    }

    public function fetchCollection($ids) {
        $results = [];
        foreach($ids as $id) {
            $results[] = $this->fetch($id);
        }
        return $results;
    }

    public function fetch($id)
    {
        $this->connect($this->getDbName());
        $query = $this->getLink()->prepare("SELECT * FROM " . $this->getTable() . " WHERE id = ? ");
        $query->execute(array($id));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if($result) {
            return $this->getMapper()->fromArray($result);
        }

        return null;
    }

    public function fetchCollectionPaginationFilters($filter, $page = 1, $limit = 10)
    {
        $this->connect($this->getDbName());
        $sql = 'SELECT * FROM ' . $this->getTable() . ' WHERE ' . $filter->getFilterNames();
        $query = $this->getLink()->prepare($sql);
        $query->execute($filter->toValues());
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        $this->close();

        $collection = [];

        foreach($results as $entity) {
            $collection[] = $this->getMapper()->fromArray($entity);
        }

        return $collection;
    }

    public function getColonPrefixedNamesArray($entity)
    {
        $entityNames = [];
        foreach ($entity->toArray() as $key => $value) {
            $entityNames[] = ':' . $key;
        }
        return $entityNames;
    }

    public function getColumnNamesArray($entity)
    {
        $columns = [];
        foreach ($entity->toArray() as $key => $value) {
            $columns[] = $key;
        }

        return $columns;
    }

    public function save($entity)
    {
        if(is_callable(array($entity, "setLastUpdateTime"))){
             $entity->setLastUpdateTime(time());
        };


        if($entity->getId() == null) {
            $sql = "INSERT into "
                . $this->getTable()
                . ' ('
                . implode(', ', $this->getColumnNamesArray($entity))
                . ') VALUES ('
                . implode(', ', $this->getColonPrefixedNamesArray($entity))
                . ')';

            $this->connect($this->getDbName());
            $query = $this->getLink()->prepare($sql);


            foreach ($this->getColonPrefixedNamesArray($entity) as $colonPrefixedVar) {
                $variableName = ltrim($colonPrefixedVar, ':');
                $getVariable = "get" . ucfirst($variableName);


                if(is_callable(array($entity, $getVariable))){
                    $variable = $entity->{$getVariable}();
                    $query->bindValue($colonPrefixedVar, $variable);
                }else {
                    echo "no variable " . $getVariable . " on entity. \n";
                    var_dump($entity);
                }
            }

            $query->execute();
            $this->close();

            return $this->fetch($this->getLink()->lastInsertId());
        }
        else {
            $updateLines = [];
            foreach ($entity->toArray() as $key => $value) {
                $updateLines[] = '`' . $key . "` = :" . $key;
            }

            $sql = "UPDATE "
                . $this->getTable()
                . ' SET '
                . implode(', ', $updateLines)
                . ' WHERE id = :id;';


            $this->connect($this->getDbName());
            $query = $this->getLink()->prepare($sql);

            foreach ($this->getColonPrefixedNamesArray($entity) as $colonPrefixedVar) {
                $variableName = ltrim($colonPrefixedVar, ':');
                $getVariable = "get" . ucfirst($variableName);


                if(is_callable(array($entity, $getVariable))){
                    $variable = $entity->{$getVariable}();
                    $query->bindValue($colonPrefixedVar, $variable);
                }
            }

//            die(__CLASS__);
            $query->execute();
            $this->close();
            return $this->fetch($entity->getId());

        }
    }

    public function errno() {
        return mysql_errno($this->link);
    }

    public function error() {
        return mysql_error($this->link);
    }

    public static function escape_string($string) {
        return mysql_real_escape_string($string);
    }

    public function query($query) {
        $this->last_sql = $query;
        return mysql_query($query, $this->link);
    }

    public function fetch_array($result, $array_type = MYSQL_BOTH) {
        return mysql_fetch_array($result, $array_type);
    }

    public function fetch_row($result) {
        return mysql_fetch_row($result);
    }

    public function fetch_assoc($result) {
        return mysql_fetch_assoc($result);
    }

    public function fetch_object($result)  {
        return mysql_fetch_object($result);
    }

    public function num_rows($result) {
        return mysql_num_rows($result);
    }

    public function close() {
        if($this->link) {
            $this->link == null;
        }
    }

    public function select_db($db) {
        return mysql_select_db($db, $this->link);
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setPass($givenPass)
    {
        $this->pass = $givenPass;
        return $this;
    }

    protected function getMapper()
    {
        return $this->mapper;
    }

    protected function setMapper($mapper)
    {
        $this->mapper = $mapper;
    }

    protected function getTable()
    {
        return $this->table;
    }

//    abstract protected function getMapper();
//    abstract protected function getTable();
//    abstract protected function getDbName();


}