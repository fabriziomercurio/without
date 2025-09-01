<?php
declare(strict_types=1); 

namespace App\Core; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

class Migrations 
{   
    public \PDO $pdo; 

    public function __construct(\PDO $pdo) 
    {   
        $this->pdo = $pdo;
    } 

    public function up(string $table) 
    {    
        $sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        id int AUTO_INCREMENT PRIMARY KEY NOT NULL,
        name varchar(255), 
        surname varchar(255), 
        age varchar(255), 
        city varchar(255)
        );"; 
        $data = $this->pdo->prepare($sql); 
        $data->execute();
    } 

    public function down(string $table) 
    {    
        $sql = "DROP TABLE IF EXISTS ".$table; 
        $data = $this->pdo->prepare($sql); 
        $data->execute();
    }

} 