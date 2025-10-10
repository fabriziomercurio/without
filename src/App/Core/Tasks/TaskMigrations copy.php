<?php
declare(strict_types=1); 

namespace App\Core\Tasks; 
use App\Core\Migration\Migrations; 
use App\Core\Migration\Schema; 

class TaskMigrations
{  
   /**
   * runs all migrations if exists in folder, 
   * except for migrations table that it will run in automatic 
   * upMigrationsTable(), create migrations' table, it's necessary for after up the others tables 
   */
    public function upAllMigrations() 
    { 
       $check = Migrations::checkIfTableExists(); 
        if (!is_array($check) && $check === false) {
           $pdo = Migrations::upMigrationsTable(); 
           if (is_object($pdo)) {
              $migrations = scandir(__DIR__ . '/../Migration/Schema/');           
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
    public function cleanMigrations() : void
    {
       $check = Migrations::removeOrphanTables();  
       if (!is_array($check) && $check === false) {
          exit('clean migration is success!'); 
       }else {
          exit('nothing to clean' . PHP_EOL); 
        }
    }

    public function downMigrations() 
    {
       Migrations::downAllTables();
    }

    public function upSingleMigration(string $tableName) : void
    {
        if (!Migrations::upMigrationsTable()) exit('error to send migrations'); 

          $filename = __DIR__ . '/Migration/Schema/'.ucfirst($tableName).'.php'; 
          if(!file_exists($filename)) exit("The file $filename does not exist" . PHP_EOL);
         
          $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName);      

          if(!class_exists($class)) exit("The class $tableName does not exist" . PHP_EOL);
          $obj = new $class; 
          $res = $obj->up(strtolower($tableName));          
          ($res === true) ? exit($tableName . ' table run with success!' . PHP_EOL) : exit("run failed" . PHP_EOL);            
    }

    public function deleteSingleMigration(string $tableName) : void 
    {
        $filename = __DIR__ . '/Migration/Schema/'.ucfirst($tableName).'.php'; 
        if(!file_exists($filename)) exit("The file $filename does not exist" . PHP_EOL);
        
        $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName);      

        if(!class_exists($class)) exit("The class $tableName does not exist" . PHP_EOL);
        $obj = new $class; 
        $res = $obj->down(strtolower($tableName)); 
        ($res === true) ? exit(" delete table with success " . PHP_EOL) : exit("no tables to delete " . PHP_EOL);
    }

}

?>