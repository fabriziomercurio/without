<?php
declare(strict_types=1); 

namespace App\Core\Migration; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

abstract class Migrations 
{   
    protected \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql); 
    }

    public function up(string $table) 
    {    
        $sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        id int AUTO_INCREMENT PRIMARY KEY NOT NULL,
        name varchar(255), 
        surname varchar(255), 
        age varchar(255), 
        city varchar(255), 
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );"; 
        $data = $this->pdo->prepare($sql); 
        $data->execute();
    } 

    public function downTable(string $table) : bool
    {    
        $sql = $this->dropTableSql($table); 
        $data = $this->pdo->prepare($sql); 
        return $data->execute();
    }

    protected function dropTableSql(string $table) : string 
    {
        $sql = "DROP TABLE IF EXISTS " . strtolower($table); 
        return $sql; 
    }

} 