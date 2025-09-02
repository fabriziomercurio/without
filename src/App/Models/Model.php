<?php 
declare(strict_types=1); 

namespace App\Models;  
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

abstract class Model 
{      
    protected static \PDO $pdo; 

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
        $sql = "INSERT INTO products (name,surname,age,city) VALUES (".$parameters.");";  
        self::pdo()->prepare($sql)->execute($data);               
        return true;        
    }    

}


?>