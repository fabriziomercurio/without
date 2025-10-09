<?php
declare(strict_types=1); 

namespace App\Core\Migration; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Builders\TableBuilder; 
use App\Interfaces\Migration;
use App\Core\ENV; 

abstract class Migrations implements Migration  
{   
    protected \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql); 
    }

    public static function upMigrationsTable() : \PDO 
    {
        $pdo = Connection::connect(new Mysql); 
        $table = new TableBuilder; 
        $table->table('migrations')
        ->addColumn('id','INT AUTO_INCREMENT PRIMARY KEY', false)
        ->addColumn('name','VARCHAR(255) UNIQUE')
        ->addColumn('batch','INT')
        ->timestamps(); 
        $query = $table->builder(); 
        $pdo->prepare($query)->execute(); 
        return $pdo;  
    }

    public function downTable(string $table) : bool
    {    
        $sql = "DROP TABLE IF EXISTS " . strtolower($table);
        $data = $this->pdo->prepare($sql); 
        return $data->execute();
    }

    public static function checkIfTableExists() : array|false
    {
        $migrations = []; 

        $pdo = Connection::connect(new MySQL); 
        $env = ENV::getContent();      

        $migrationFiles = scandir(__DIR__ . '/Schema/'); 

        foreach ($migrationFiles as $migrate) { 

            if ($migrate === '.' || $migrate === '..') {
                continue; 
            }
            
            if (pathinfo($migrate, PATHINFO_EXTENSION) === 'php') {
              $pos = strpos($migrate, '.php');
              $className = substr($migrate, 0, $pos); 

              $class = "App\\Core\\Migration\\Schema\\". $className;

              if (class_exists($class)) {              
                 $instance = new $class;

                 if ($instance instanceof Migrations && method_exists($instance,'up')) {
                     $migrations[] = strtolower($className); 
                 }                            
              } 
            }    
        }

        $migrations[] = 'migrations';

        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = '".ENV::$config["DATABASE"]."'
            AND table_name IN ($placeHolders);"; 

        $data = $pdo->prepare($sql); 
        $data->execute($migrations); 
        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN); 

        if (count($countTables) !== count($migrations)) {
            echo 'entro'; 
            return false; 
        } 

        return $countTables; 
    } 

    public static function cleanMigrationsIfFilesNotExists() 
    {
        $migrations = [];        

        $pdo = Connection::connect(new MySQL); 
        $env = ENV::getContent();      

        $migrationFiles = scandir(__DIR__ . '/Schema/');

        foreach ($migrationFiles as $migrate) {

            if ($migrate === '.' || $migrate === '..') {
                continue; 
            } 

            if (pathinfo($migrate, PATHINFO_EXTENSION) === 'php') {
              $pos = strpos($migrate, '.php');
              $className = substr($migrate, 0, $pos); 

              $class = "App\\Core\\Migration\\Schema\\". $className;

              if (class_exists($class)) {              
                 $instance = new $class;

                 if ($instance instanceof Migrations && method_exists($instance,'up')) {
                     $migrations[] = strtolower($className); 
                 }                            
              } 
            }
        } 
        $migrations[] = 'migrations'; 
        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = '".ENV::$config["DATABASE"]."'"; 

        $data = $pdo->prepare($sql); 
        $data->execute(); 
        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN);  

        if (count($countTables) !== count($migrations)) {
            
        $result=array_diff($countTables,$migrations);

            foreach ($result as $tableName) {
                $sql = "DROP TABLE IF EXISTS " . $tableName; 
                $data = $pdo->prepare($sql); 
                $data->execute(); 
            }
 
            return false; 
        } 

        return $countTables; 
    }
  

} 

?>