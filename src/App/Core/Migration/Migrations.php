<?php
declare(strict_types=1); 

namespace App\Core\Migration; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Builders\TableBuilder; 
use App\Interfaces\Migration;

abstract class Migrations implements Migration  
{   
    protected \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql); 
    }

    public static function upAllMigrations() : \PDO 
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

} 

?>