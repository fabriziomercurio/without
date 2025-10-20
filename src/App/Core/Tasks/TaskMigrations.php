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
      $this->pdo = Connection::connect(new MySQL);
   }

   /**
   * runs all migrations if exists in folder, 
   * except for migrations table that it will run in automatic 
   * upMigrationsTable(), create migrations' table, 
   * it's necessary for after up the others tables 
   */
    public function upAllMigrations() 
    { 
       $check = Migrations::checkIfAllTablesExists(); 
       if (is_array($check) && $check !== false) exit('nothing to migrate' . PHP_EOL); 
           $pdo = Migrations::upMigrationsTable(); 
           if (!is_object($pdo)) exit('error to send migrations'); 
              $migrations = scandir(__DIR__ . '/../Migration/Schema/');           
              foreach ($migrations as $migration) {
                   if($migration === '.' || $migration === '..') continue; 
                     if (str_contains($migration, '.php')) {
                       $pos = strpos($migration, '.php'); 
                       $tableName = substr($migration, 0, $pos);
                       $class = "App\\Core\\Migration\\Schema\\". $tableName;       
                       if (!class_exists($class)) exit('Class $class not found');
                        $obj = new $class; 
                        $obj->up($tableName); 

                        $res = Migrations::insertInMigrationTable($tableName);  

                        if ($res === false) exit('migrations table can\'t populates'); 
                     } 
              }
               exit('run all migrations success!' . PHP_EOL); 
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
         $this->pdo->beginTransaction();

         try {

           if (!Migrations::upMigrationsTable()) exit('error to send migrations'); 

           $filename = __DIR__ . '/../Migration/Schema/'.ucfirst($tableName).'.php'; 
           if(!file_exists($filename)) exit("The file $filename does not exist" . PHP_EOL);
           
           $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName);      

           if(!class_exists($class)) exit("The class $tableName does not exist" . PHP_EOL);
           $obj = new $class; 
           $res = $obj->up(strtolower($tableName)); 
           
           Migrations::insertInMigrationTable($tableName);  

           exit($tableName . ' table run with success!' . PHP_EOL);

         } catch (\Throwable $th) {
           $this->pdo->rollBack(); 
           exit('impossible to send a specific table ' . $th->getMessage());
         }
    }

    public function deleteSingleMigration(string $tableName) : void 
    {  
         $this->pdo->beginTransaction();

         try {
         $filename = __DIR__ . '/../Migration/Schema/'.ucfirst($tableName).'.php'; 
         if(!file_exists($filename)) exit("The file $filename does not exist" . PHP_EOL);
         
         $class = "App\\Core\\Migration\\Schema\\". ucfirst($tableName);      

         if(!class_exists($class)) exit("The class $tableName does not exist" . PHP_EOL);
         $obj = new $class; 
         $res = $obj->down(strtolower($tableName)); 

         Migrations::deleteInMigrationTable($tableName); 

         ($res === true) ? exit(" delete table with success " . PHP_EOL) : exit("no tables to delete " . PHP_EOL); 

         } catch (\Throwable $th) {
            $this->pdo->rollBack(); 
            exit('impossible to send a specific table ' . $th->getMessage());
         }
    }

}

?>