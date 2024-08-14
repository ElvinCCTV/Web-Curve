<?php

require_once __DIR__ . '/traits/HelperTrait.php';
ini_set('error_log', '/home/elvin/Desktop/error.log');
abstract class Model
{
    use HelperTrait;
    public $dbConnection;
    public $table;
    public $getMethodSql;
    public $getMethodExecuteArray = [];
    public $getMethodWhereCount = 0;
    public $getMethodOrWhereCount = 0;

    private $selectFields = '*';


    public function __construct()
    {
        $this->dbConnection();
        $this->setTable();
    }

    protected function dbConnection()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "simple_crud";

        try {
            $this->dbConnection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    protected function setTable()
    {
        if (is_null($this->table)) {
            $className = strtolower(__CLASS__); //get_called_class()

            $this->table = $this->singularToPlural($className);
        }
    }


    public function all()
    {
        $sql = "SELECT * FROM $this->table";
        $run = $this->dbConnection->prepare($sql);
        $run->execute();
        return $run->fetchAll(PDO::FETCH_OBJ);
    }
    public function totalRows()
    {
        $sql = "SELECT COUNT(*) FROM $this->table";
        $run = $this->dbConnection->prepare($sql);
        $run->execute();
        return $run->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT $this->selectFields FROM $this->table WHERE id = :id LIMIT 1";
        $run = $this->dbConnection->prepare($sql);
        $run->execute([':id' => $id]);
        return $run->fetch(PDO::FETCH_OBJ);
    }

    public function paginate($usersAmount, $offset)
    {

      
        $sql = "SELECT $this->selectFields FROM users LIMIT :lmt OFFSET :fff";
        $run = $this->dbConnection->prepare($sql);
        
        // Bind parameters as integers
        $run->bindParam(':lmt', $usersAmount, PDO::PARAM_INT);
        $run->bindParam(':fff', $offset, PDO::PARAM_INT);
        
        $run->execute();
        return $run->fetchAll(PDO::FETCH_OBJ);
    }

    public function select(...$fields)
    {
        if (count($fields) > 0) {
            $this->selectFields = implode(',', $fields);

        }
        return $this;
    }

    public function where($column, $operator, $value, $singleWhere)
    {   
        $contWhere = ++$this->getMethodWhereCount;
        if($singleWhere){
            $contWhere = 1;
            $this->getMethodSql = "";
            $this->getMethodExecuteArray = [];
        }

        $tokens = $column . $this->guidv4();


        if ($contWhere == 1) {
            $this->getMethodSql = " WHERE $column $operator :$tokens";
        } else {
            $this->getMethodSql .= " AND  $column $operator :$tokens";
        }
        $this->getMethodExecuteArray[$tokens] = $value;

        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $tokens = $column . $this->guidv4();



        if ($this->getMethodWhereCount < 1) {
            $this->getMethodSql .= " WHERE  $column $operator :$tokens";
        } else {
            $this->getMethodSql .= " OR  $column $operator :$tokens";
        }
        $this->getMethodExecuteArray[$tokens] = $value;

        return $this;
    }

    public function get()
    {
        try {
            $mainSql = "SELECT  $this->selectFields FROM $this->table";
            if (!empty($this->getMethodSql)) {
                $mainSql .= $this->getMethodSql;


            }
            $run = $this->dbConnection->prepare($mainSql);

            if (count($this->getMethodExecuteArray) > 0) {
                $run->execute($this->getMethodExecuteArray);

            } else {

                $run->execute();
            }
            return $run->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function delete()
    {
        $mainSql = "DELETE  FROM $this->table";
        if (!empty($this->getMethodSql)) {
            $mainSql .= $this->getMethodSql;
        }
        $run = $this->dbConnection->prepare($mainSql);
        if (count($this->getMethodExecuteArray) > 0) {
            $run->execute($this->getMethodExecuteArray);
        } else {
            $run->execute();
        }
        if ($run->rowCount() > 0) {
            return 1;
        }
        return 0;
    }

    public function create($data)
    {
        $mainSql = "INSERT INTO $this->table (";
    
        $columns = array_keys($data);
        $placeholders = [];
        $executeData = [];
    
        foreach ($columns as $column) {
            $placeholder = ":$column";
            $placeholders[] = $placeholder;
            $executeData[$placeholder] = $data[$column];
        }
    
        $mainSql .= implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    
        $run = $this->dbConnection->prepare($mainSql);
        $run->execute($executeData);
    
        if ($run->rowCount() > 0) {
            return 1;
        }
    
        return 0;
    }
    

    public function update($data)
    {

        $mainSql = "UPDATE $this->table  SET ";
        $generateSqlPrepareToken = $this->generateSqlPrepareToken($data);
        $mainSql .= $generateSqlPrepareToken['sqlTokens'];

        if (!empty($this->getMethodSql)) {
            $mainSql .= $this->getMethodSql;
        }

        try {
            $run = $this->dbConnection->prepare($mainSql);
            $executeDataArray = array_merge($this->getMethodExecuteArray, $generateSqlPrepareToken['executeData']);


            // echo json_encode( $mainSql . print_r($executeDataArray)); die;
            $run->execute($executeDataArray);
            // echo  json_encode($run->rowCount()); die;
            return $run->rowCount();

        } catch (Exception $e) {
            return 0;
        }

    }

    private function generateSqlPrepareToken($data)
    {
        $dataTokens = array_keys($data);
        $tokens = [];
        $executeData = [];
        foreach ($dataTokens as $value) {
            $tokenVal = ":$value" . $this->guidv4();
            $tokens[] = "$value = $tokenVal";
            $executeData[$tokenVal] = $data[$value];
        }
        // =implode(',',$tokens);

        return [
            'executeData' => $executeData,
            'sqlTokens' => implode(',', $tokens),
        ];
    }

}
