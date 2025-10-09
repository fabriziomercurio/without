<?php
declare(strict_types=1); 

namespace App\Core; 
use App\Core\Migration\Migrations; 
use App\Core\Migration\Schema; 

class TaskManager
{
   /**
   * runs all migrations if exists in folder, 
   * except for migrations table that it will run in automatic 
   */
    public function upAllMigrations() 
    {
        $check = Migrations::checkIfTableExists(); 
        if (!is_array($check) && $check === false) {
           $pdo = Migrations::upMigrationsTable(); 
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
        } else {
          exit('nothing to migrate' . PHP_EOL); 
        }
    } 
    
    /**
     * deletes tables if the its files not exists in folders 
     */
    public function cleanMigrations() 
    {
       $check = Migrations::cleanMigrationsIfFilesNotExists();  
       if (!is_array($check) && $check === false) {
          exit('clean migration is success!'); 
       }else {
          exit('nothing to clean' . PHP_EOL); 
        }
    }
}

?>