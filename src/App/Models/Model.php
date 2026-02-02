<?php 
declare(strict_types=1); 

namespace App\Models;  
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Request; 

abstract class Model 
{      
    protected static \PDO $pdo; 

    public static function pdo() : \PDO
    {        
        if (!isset(self::$pdo)) {
            return self::$pdo = Connection::connect(new Mysql); 
        }
        return self::$pdo; 
    } 

    public static function fetchAllData(string $table) : array
    {
        $stmt = self::pdo()->prepare("SELECT * FROM ".$table);
        $stmt->execute(); 
        return $stmt->fetchAll(); 
    }   

    public function storeData($data, string $table) : string|false 
    {   
        $parameters = implode(",", array_map(function($n){ return ":".$n; }, array_keys($data)));  
        $values = implode(",",array_keys($data)); 
        $sql = "INSERT INTO ".$table." (".$values.") VALUES (".$parameters.");";
        self::pdo()->prepare($sql)->execute($data); 
        return self::pdo()->lastInsertId();
    } 

    public static function editRecord(int $id, string $table) : bool | array  
    {
        $sql = "SELECT * FROM ".$table." WHERE id = :id";  
        $sth = self::pdo()->prepare($sql); 
        $sth->execute(array('id' => $id)); 
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    public static function updateRecord(int $id, Request $request, string $table) : bool 
    {   
        $data = $request->getBody(); 
        $parameters = implode(",", array_map(function($n){ return $n . " = :" . $n; }, array_keys($data)));  
        $sql = "UPDATE " . $table . " SET " . $parameters . " WHERE id = :id"; 
        $sth = self::pdo()->prepare($sql);  
        $sth->execute(array_merge($data,['id' => $id]));
        return true;      
    }

    public static function deleteRecord(int $id, string $table) : bool
    {       
        $sth = self::pdo()->prepare("DELETE FROM ".$table." WHERE id = :id");
        $sth->execute(array('id' => $id));
        return true;
    }

    public function loadData(array $array) 
    {   
        foreach ($array as $key => $value) {
            if (property_exists($this,$key)) {
                $this->{$key} = $value;         
            }
        } 
    } 

    /* Takes the values ​​from the fillable method 
       and checks if they match the alias key. 
       The field is wrapped in $data and records its value, 
       e.g., $this->alias_name 
    */ 
    protected function getDatabaseAttributes() : array 
    {  
       $data = [];  
       foreach ($this->fillable() as $field) {       
            $property = $this->alias[$field] ?? $field;
            $data[$field] = $this->{$property}; 
       } 
       return $data; 
    } 

    /**
     * The fillable is generally used when we have aliases inside the form, 
     * this is because the contents of the array will dynamically create the fields of the table, 
     * using an alias in this context would break the table because the names of the fields would not be the same
     */
    abstract protected function fillable(): array;
}


?>