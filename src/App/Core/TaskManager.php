<?php
declare(strict_types=1); 

namespace App\Core; 
use App\Core\Migration\Migrations; 
use App\Core\Migration\Schema; 

class TaskManager
{
    public function upAllMigrations() 
    {
        $pdo = Migrations::upAllMigrations(); 
        if (is_object($pdo)) {
           $migrations = scandir(__DIR__ . '/Migration/Schema/');           
           foreach ($migrations as $migration) {
                if($migration === '.' || $migration === '..') continue; 
                  if (str_contains($migration, '.php')) {
                    $pos = strpos($migration, '.php'); 
                    $tableName = substr($migration, 0, $pos);
                    $class = "App\\Core\\Migration\\Schema\\". $tableName;       
                    if (class_exists($class)) {
                      $obj = new $class; 
                      $obj->up($tableName);
                    }else {
                        exit('Class $class not found'); 
                    }                            
                  }
           }
            exit('run all migrations success!'); 
        }else {
            exit('error to send migrations'); 
        } 
    }
}

?>