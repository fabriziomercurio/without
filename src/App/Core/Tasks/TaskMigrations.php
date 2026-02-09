<?php
declare(strict_types=1); 

namespace App\Core\Tasks; 
use App\Core\Migration\Migrations; 
use App\Core\Migration\Schema; 
use App\Core\Connections\MySQL;
use App\Core\Connections\Connection; 

class TaskMigrations
{  
   private \PDO $pdo; 

   public function __construct() 
   {
      Connection::connect(new MySQL); // or Migration::Conn();
   }

   /**
   * runs all migrations if exists in folder, 
   * except for migrations table that it will run in automatic 
   * upMigrationsTable(), create migrations' table, 
   * it's necessary for after up the others tables,
   * checkIfAllTablesExists() checks if all tables are present in db
   */
    public function upAllMigrations() 
    { 
      try {
         $check = Migrations::checkIfAllTablesExists();  
       if (is_array($check) && $check !== false) throw new \Exception('nothing to migrate' . PHP_EOL); 
           $pdo = Migrations::upMigrationsTable(); 
           if ($pdo === false) throw new \Exception('error to send migrations'); 
              $migrations = scandir(__DIR__ . '/../Migration/Schema/');        
              foreach ($migrations as $migration) {
                   if($migration === '.' || $migration === '..') continue; 
                     if (str_contains($migration, '.php')) {
                      $fileNameMigration = substr($migration, 0, strpos($migration, '.php'));
                    
                     if (preg_match('/^20\\d{2}_\\d{2}_\\d{2}_\\d{6}_create_(.+)_table$/', $fileNameMigration, $matches)) {
                      $class = ucfirst($matches[1]); // class name  
                     } 
                        
                      $fullPath = __DIR__ . '/../Migration/Schema/'.$migration;
                      if(!file_exists($fullPath))  throw new \Exception("File `$fullPath` not found".PHP_EOL); 

                      require_once $fullPath; 

                      $fullClass = "App\\Core\\Migration\\Schema\\". $class;     
                
                      if (!class_exists($fullClass)) throw new \Exception("Class `$class` not found in file `$migration`"); 

                       $obj = new $fullClass;                        
                       $obj->up(strtolower($class)); 
  
                       $res = Migrations::insertInMigrationTable($matches[1]); 
                   
                       if ($res === false) throw new \Exception('migrations table can\'t populates'); 
                     } 
              } 
               echo json_encode('run all migrations success!' . PHP_EOL);
      } catch (\Throwable $th) {
         echo json_encode($th->getMessage().' at line ' . $th->getLine()); 
      } 
    } 

    public function downMigrations() 
    {      
      try {
         Migrations::downAllTables();  
         echo json_encode('delete all migrations'); 
      } catch (\Throwable $th) {
         echo json_encode($th->getMessage());
      }
    }

    public function upSingleMigration(string $tableName) 
    {          
         try {

           if (!Migrations::upMigrationsTable()) throw new \Exception('error to send migration table'); 

           if (!is_dir(__DIR__ . '/../Migration/Schema/')) throw new \Exception('SubFolder Schema in Migration folder doesn\'t exists' . PHP_EOL); 
             
           $migrations = scandir(__DIR__ . '/../Migration/Schema/'); 

           foreach ($migrations as $migration) {        
              if($migration === '.' or $migration === '..') continue; 

              $matches = Migrations::formatFileNameMigration($migration); 

              if (is_array($matches) && $matches[1] === strtolower($tableName)) { 
             
                     $fullPath = __DIR__ . '/../Migration/Schema/'.$migration;

                     if(!file_exists($fullPath)) throw new \Exception("The path ".ucfirst($fullPath)." does not exist" . PHP_EOL); 
                     
                     require_once $fullPath;

                     $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName); 

                     if(!class_exists($class)) throw new \Exception("The class ".ucfirst($tableName)." does not exist" . PHP_EOL); 
                     
                     $obj = new $class; 
                     $res = $obj->up(strtolower($tableName));                   
                     Migrations::insertInMigrationTable($tableName);                   
                     echo json_encode($tableName . ' table run with success!' . PHP_EOL);
                     return; 
               }else {
                 $except = json_encode(ucfirst($tableName).' class is not exists for up migrate'.PHP_EOL); 
               }                         
           }         
           echo isset($except) ? $except : '';
         } catch (\Throwable $th) {
           echo json_encode('impossible to send a specific table ' . $th->getMessage());
         }
    }

    public function deleteSingleMigration(string $tableName) : void 
    {  
         try { 
         $migrations = scandir(__DIR__ . '/../Migration/Schema/');
         foreach ($migrations as $migration) {
            if ($migration === '.' or $migration === '..') continue; 

            $matches = Migrations::formatFileNameMigration($migration);          
         
               if ($matches[1] === strtolower($tableName)) {

                     $fullPath = __DIR__ . '/../Migration/Schema/'.$migration;
                     if(!file_exists($fullPath)) throw new \Exception("The path ".ucfirst($fullPath)." does not exist" . PHP_EOL); 
                     require_once $fullPath;

                     $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName);
                     if(!class_exists($class)) throw new \Exception("The class ".ucfirst($tableName)." does not exist" . PHP_EOL); 

                     $obj = new $class; 
                     $res = $obj->down(strtolower($tableName));                   
                     Migrations::deleteInMigrationTable($tableName);   
                    if ($res === true) { echo json_encode("delete table with success " . PHP_EOL); } else { throw new \Exception("no tables to delete"); }
                   
               }else {
                   $except = throw new \Exception(ucfirst($tableName).' class is not exists for delete migrate'.PHP_EOL);
                   return;
               }       
            }
         echo isset($except) ? $except : '';
         } catch (\Throwable $th) { 
            if ($th->getCode() === 1001) echo json_encode($th->getMessage()); return;
            echo json_encode('impossible to send a specific table ' . $th->getMessage());
         }
    }

    /**
     * create an automatic migration file 
     */
   public function createMigration(string $string) 
   {
      Migrations::createMigration($string); 
   } 

   

}

?>