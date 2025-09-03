<?php 
declare(strict_types=1); 

namespace App\Models;  
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

abstract class Model 
{      
    protected static \PDO $pdo; 

    protected const RULE_REQUIRED = 'required'; 

    public static function pdo() : \PDO
    {        
        if (!isset(self::$pdo)) {
            return self::$pdo = Connection::connect(new Mysql); 
        }
    } 

    public static function fetchAllData(string $table) : array
    {
        $stmt = self::pdo()->prepare("SELECT * FROM ".$table);
        $stmt->execute(); 
        return $stmt->fetchAll(); 
    }   

    public function storeData(object $data, string $table) : bool
    {   
        $data = (array)$data;          
        $parameters = implode(",", array_map(function($n){ return ":".$n; }, array_keys($data)));  
        $values = implode(",",array_keys($data)); 
        $sql = "INSERT INTO ".$table." (".$values.") VALUES (".$parameters.");";   
        self::pdo()->prepare($sql)->execute($data); 
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

    public function validate() 
    {   
        $array = []; 
        foreach ($this->rules() as $attribute => $rules) { 
            $value = $this->{$attribute}; 
            foreach ($rules as $rule) {
                if ($rule === self::RULE_REQUIRED && !$value) {
                  $array[$attribute] = $this->addError($attribute,$rule); 
                } 
            }          
        } 
        
         return $array;   
    }

    private function addError(string $attribute, string $rule) 
    {   
        return $this->getErrors($attribute,$rule)[$rule]; 
    } 

    private function getErrors(string $attribute, string $rule) 
    {
        return [
            self::RULE_REQUIRED => $attribute.' is ' . $rule,
        ]; 
    }

}


?>