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

    public static function downAllTables() 
    {
        $pdo = Connection::connect(new MySQL); 
        $env = ENV::getContent(); 

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ?"; 
        $data = $pdo->prepare($sql); 
        $res = $data->execute([ENV::$config['DATABASE']]); 
        if ($res) {
            foreach($data->fetchAll(\PDO::FETCH_COLUMN) as $table) 
            {   
                $pdo->exec('DROP TABLE IF EXISTS ' . $table);                         
            } 
            exit('delete all migrations'); 
        } 
        exit('db not found');         
    }

    public static function checkIfTableExists() : array|false
    {
        $pdo = Connection::connect(new MySQL); 
        $env = ENV::getContent();      

        $migrations = self::scandirCustom([]); 

        $migrations[] = 'migrations';

        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = self::queryTable(ENV::$config["DATABASE"],$placeHolders, true);

        $data = $pdo->prepare($sql); 
        $params = array_merge([ENV::$config["DATABASE"]], $migrations);
        $data->execute($params); 

        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN); 

        if (count($countTables) !== count($migrations)) return false;     

        return $countTables; 
    } 

    private static function scandirCustom(array $migrations) 
    {
        $migrationFiles = scandir(__DIR__ . '/Schema/'); 

        foreach ($migrationFiles as $migrate) { 

            if ($migrate === '.' || $migrate === '..') continue;         
            
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

        return $migrations;
    }

    private static function queryTable(string $db, string $placeHolders, bool $condition = false) : string
    {
        $addToQuery = ''; 

        if ($condition === true) {
            $addToQuery = "AND table_name IN ($placeHolders);";
        } 

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ?".$addToQuery;
        return $sql; 
    }

    public static function removeOrphanTables() 
    {
        $pdo = Connection::connect(new MySQL); 
        $env = ENV::getContent();      

        $migrations = self::scandirCustom([]);

        $migrations[] = 'migrations'; 
        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = self::queryTable(ENV::$config["DATABASE"],'',false);

        $data = $pdo->prepare($sql); 
        $data->execute([ENV::$config["DATABASE"]]); 
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